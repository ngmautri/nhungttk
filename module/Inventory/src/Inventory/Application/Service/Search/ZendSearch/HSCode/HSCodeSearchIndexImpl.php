<?php
namespace Inventory\Application\Service\Search\ZendSearch\HSCode;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Entity\InventoryHsCode;
use Application\Service\AbstractService;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Service\Search\HSCode\HSCodeSearchIndexInterface;
use Inventory\Infrastructure\Persistence\Doctrine\HSCodeReportRepositoryImpl;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\SearchIndexInterface;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Document\Field;
use Exception;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeSearchIndexImpl extends AbstractService implements HSCodeSearchIndexInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\HSCode\HSCodeSearchIndexInterface::createIndex()
     */
    public function createIndex($rows)
    {
        $indexResult = new IndexingResult();
        $currentSnapshot = null;

        try {

            $rep = new HSCodeReportRepositoryImpl($this->getDoctrineEM());
            $rows = $rep->getList();

            if (count($rows) == 0) {
                throw new \InvalidArgumentException("No input not given");
            }

            // take long time
            set_time_limit(3500);

            $indexer = Lucene::create(getcwd() . SearchIndexer::INDEX_PATH);

            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $entity) {
                $this->_createNewIndexFromEntity($indexer, $entity);
            }

            $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);

            $this->getLogger()->info($message);
        } catch (Exception $e) {

            echo $e->getTraceAsString();

            $m = '??';
            if ($currentSnapshot !== null) {
                $m = $currentSnapshot->getId();
            }

            $message = \sprintf('Failed! %s - %s', $e->getMessage(), $m);

            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
            $this->getLogger()->error($message);
        }

        return $indexResult;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\HSCode\HSCodeSearchIndexInterface::optimizeIndex()
     */
    public function optimizeIndex()
    {
        $indexResult = new IndexingResult();

        try {
            set_time_limit(1500);

            $index = $this->getIndexer();
            $index->optimize();
            $indexResult = $this->_updateIndexingResult($index, $indexResult);
            $message = \sprintf('Index has been optimzed successfully! %s', $indexResult->getIndexDirectory());
            $indexResult->setMessage($message);
            $this->getLogger()->info($message);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s', $e->getMessage());
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
            $this->getLogger()->error($message);
        }

        return $indexResult;
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param IndexingResult $indexResult
     * @return \Application\Application\Service\Search\Contracts\IndexingResult
     */
    private function _updateIndexingResult(SearchIndexInterface $indexer, IndexingResult $indexResult)
    {
        $indexResult->setDocsCount($indexer->numDocs());
        $indexResult->setIndexSize($indexer->count());
        $indexResult->setIndexVesion($indexer->getFormatVersion());

        if ($indexer->getDirectory() !== null) {
            $indexResult->setFileList($indexer->getDirectory()
                ->fileList());
        }
        $indexResult->setIndexDirectory(SearchIndexer::INDEX_PATH);
        return $indexResult;
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $indexer
     * @param ItemSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _createNewIndexFromEntity(SearchIndexInterface $indexer, InventoryHsCode $entity)
    {
        if (! $entity instanceof InventoryHsCode) {
            throw new \InvalidArgumentException("InventoryHsCode empty");
        }

        $doc = new Document();

        $doc->addField(Field::unIndexed('hsCode_id', $entity->getId()));
        $doc->addField(Field::unIndexed('parentCode_id', $entity->getParentId()));

        /*
         * $doc->addField(Field::unIndexed('hsCode_val', $entity->getHsCode()));
         * $doc->addField(Field::unIndexed('parentCode_val', $entity->getParentCode()));
         */
        $doc->addField(Field::keyword('hsCode_key', $entity->getHsCode()));
        $doc->addField(Field::keyword('parentCode_key', $entity->getParentCode()));
        $doc->addField(Field::text('hsCode', $entity->getHsCode()));
        $doc->addField(Field::text('parentCode', $entity->getParentCode()));
        $doc->addField(Field::text('codeDescription', $entity->getCodeDescription()));
        $doc->addField(Field::text('codeDescription1', $entity->getCodeDescription1()));
        $indexer->addDocument($doc);
    }

    /**
     *
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    private function getIndexer()
    {
        $indexer = null;
        try {
            $indexer = Lucene::open(getcwd() . SearchIndexer::INDEX_PATH);
        } catch (RuntimeException $e) {
            $indexer = Lucene::create(getcwd() . SearchIndexer::INDEX_PATH);
        }

        return $indexer;
    }
}
