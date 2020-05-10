<?php
namespace Procure\Application\Service\Search\ZendSearch\PO;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Service\AbstractService;
use Procure\Domain\DocSnapshot;
use Procure\Domain\GenericRow;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\Search\PoSearchIndexInterface;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Lucene;
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
class PoSearchIndexImpl extends AbstractService implements PoSearchIndexInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchIndexInterface::optimizeIndex()
     */
    public function optimizeIndex()
    {
        $indexResult = new IndexingResult();

        try {
            set_time_limit(1500);

            $index = $this->getIndexer();
            $index->optimize();
            $indexResult = $this->_updateIndexingResult($index, $indexResult);
            $message = \sprintf('Index has been optimzed successfully! %s', '');
            $indexResult->setMessage($message);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s', $e->getMessage());
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
        }

        return $indexResult;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchIndexInterface::createDoc()
     */
    public function createDoc(DocSnapshot $doc)
    {
        try {

            $indexResult = new IndexingResult();

            if (! $doc instanceof POSnapshot) {
                throw new \InvalidArgumentException("PoSnapshot not given");
            }

            $rows = $doc->getDocRows();

            if ($rows == null) {
                throw new \InvalidArgumentException("PoSnapshot empty");
            }

            // take long time
            set_time_limit(1500);

            $index = $this->getIndexer();
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {
                $this->_createDoc($index, $row);
            }

            $message = \sprintf('Document has been added successfully! %s', $doc->getId());

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($index, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s', $e->getMessage());

            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
        }

        return $indexResult;
    }

    public function createIndex()
    {}

    public function updateDoc(DocSnapshot $doc)
    {}

    public function updateIndex($entityId, $isNew = true, $optimized = false)
    {}

    private function _updateIndexingResult($index, IndexingResult $indexResult)
    {
        $indexResult->setDocsCount($index->numDocs());
        $indexResult->setIndexSize($index->count());
        $indexResult->setIndexVesion($index->getFormatVersion());

        if ($index->getDirectory() !== null) {
            $indexResult->setFileList($index->getDirectory()
                ->fileList());
        }
        $indexResult->setIndexDirectory(PoSearch::INDEX_PATH);
        return $indexResult;
    }

    private function _createDoc(\ZendSearch\Lucene\SearchIndexInterface $index, GenericRow $row)
    {
        $doc = new Document();

        if (! $row instanceof PORow) {
            throw new \InvalidArgumentException("PoRow empty");
        }

        $ck_query = \sprintf('po_doc_row_key:%s', \sprintf('%s_%s', $row->getDocId(), $row->getId()));

        $ck_hits = $index->find($ck_query);
        echo (count($ck_hits));

        if (count($ck_hits) > 0) {

            foreach ($ck_hits as $hit) {
                $index->delete($hit->id);
            }
        }

        $doc->addField(Field::UnIndexed('po_id', $row->getDocId()));
        $doc->addField(Field::UnIndexed('po_token', $row->getDocToken()));
        $doc->addField(Field::UnIndexed('po_row_id', $row->getId()));
        $doc->addField(Field::UnIndexed('po_row_token', $row->getToken()));
        $doc->addField(Field::UnIndexed('row_quantity', $row->getDocQuantity()));
        $doc->addField(Field::UnIndexed('row_conversion_factor', $row->getConversionFactor()));
        $doc->addField(Field::UnIndexed('row_unit', $row->getDocUnit()));
        $doc->addField(Field::UnIndexed('row_unit_price', $row->getDocUnit()));

        $doc->addField(Field::Keyword('po_doc_row_key', \sprintf('%s_%s', $row->getDocId(), $row->getId())));
        $doc->addField(Field::Keyword('row_identifer_key', $row->getRowIdentifer()));
        $doc->addField(Field::Keyword('po_sys_number', $row->getDocSysNumber()));
        $doc->addField(Field::Keyword('po_doc_status', $row->getDocStatus()));

        $doc->addField(Field::text('row_remarks', $row->getRemarks()));
        $doc->addField(Field::text('row_name', $row->getVendorItemCode()));
        $doc->addField(Field::text('po_number', $row->getDocNumber()));

        // vendor
        $doc->addField(Field::UnIndexed('vendor_id', $row->getVendorId()));
        $doc->addField(Field::UnIndexed('vendor_token', $row->getVendorToken()));
        $doc->addField(Field::Keyword('vendor_id_key', \sprintf('vendor_id_key_%s', $row->getVendorId())));

        // Item
        $doc->addField(Field::UnIndexed('item_id', $row->getItem()));
        $doc->addField(Field::UnIndexed('item_token', $row->getItemToken()));

        $doc->addField(Field::Keyword('item_id_key', \sprintf('item_id_key_%s', $row->getItem())));
        $doc->addField(Field::Keyword('item_sku_key', $row->getItemSKU()));
        $doc->addField(Field::text('item_name', $row->getItemName()));

        $index->addDocument($doc);
    }

    private function getIndexer()
    {
        $indexer = null;
        try {
            $indexer = Lucene::open(getcwd() . PoSearch::INDEX_PATH);
        } catch (RuntimeException $e) {
            $indexer = Lucene::create(getcwd() . PoSearch::INDEX_PATH);
        }

        return $indexer;
    }
}
