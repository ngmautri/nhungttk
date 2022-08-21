<?php
namespace HR\Application\Service\Search\ZendSearch\Individual;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Service\AbstractService;
use HR\Domain\Employee\IndividualSnapshot;
use HR\Domain\Service\Search\IndividualSearchIndexInterface;
use Inventory\Domain\Item\GenericItem;
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
class IndividualSearchIndexImpl extends AbstractService implements IndividualSearchIndexInterface
{

    public function createIndex($rows)
    {
        $indexResult = new IndexingResult();
        $currentSnapshot = null;

        try {

            if (count($rows) == 0) {
                throw new \InvalidArgumentException("No input not given");
            }

            // take long time
            set_time_limit(3500);

            $indexer = Lucene::create(getcwd() . SearchIndexer::INDEX_PATH);

            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $snapshot) {
                $currentSnapshot = $snapshot;
                $this->_createIndexForSnapshot($indexer, $snapshot);
            }

            // $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            // $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {

            $this->logException($e);
            $m = '??';
            if ($currentSnapshot !== null) {
                $m = $currentSnapshot->getId();
            }

            $message = \sprintf('Failed! %s - %s', $e->getMessage(), $m);

            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
        }

        return $indexResult;
    }

    public function createNewIndex($rows)
    {
        $indexResult = new IndexingResult();
        $currentSnapshot = null;

        try {

            // take long time
            set_time_limit(10000);

            $indexer = Lucene::create(getcwd() . SearchIndexer::INDEX_PATH);

            Analyzer::setDefault(new CaseInsensitive());

            /**
             *
             * @var GenericItem $item ;
             */
            foreach ($rows as $item) {

                $item->getLazyVariantCollection();
                $item->getLazySerialCollection();
                $currentSnapshot = $item->makeSnapshot();
                $this->_createIndexForSnapshot($indexer, $currentSnapshot);
            }

            // $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            // $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {

            $this->logException($e);
            $m = '??';
            if ($currentSnapshot !== null) {
                $m = $currentSnapshot->getId();
            }

            $message = \sprintf('Failed! %s - %s', $e->getMessage(), $m);

            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
        }

        return $indexResult;
    }

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

    public function createDoc(IndividualSnapshot $snapshot)
    {
        try {

            $indexResult = new IndexingResult();

            if (! $snapshot instanceof IndividualSnapshot) {
                throw new \InvalidArgumentException("IndividualSnapshot not given");
            }

            // take long time
            set_time_limit(1500);

            $indexer = $this->getIndexer();
            Analyzer::setDefault(new CaseInsensitive());

            $this->_createIndexForSnapshot($indexer, $snapshot);

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            $message = \sprintf('Search index created. %s', $snapshot->getId());
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {
            $this->logException($e);
            $message = \sprintf('Failed! %s', $e->getMessage());
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
        }

        return $indexResult;
    }

    /**
     *
     * @param SearchIndexInterface $indexer
     * @param IndividualSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _createIndexForSnapshot(SearchIndexInterface $indexer, IndividualSnapshot $snapshot)
    {
        if (! $snapshot instanceof IndividualSnapshot) {
            throw new \InvalidArgumentException("IndividualSnapshot empty");
        }

        /*
         * |=================================
         * | Delete, if exits
         * |
         * |==================================
         */

        $k = SearchIndexer::INDIVIDUAL_KEY;
        $v = \sprintf(SearchIndexer::INDIVIDUAL_KEY_VALUE_FORMAT, $snapshot->getId());

        $ck_query = \sprintf('%s:%s', $k, $v);

        $ck_hits = $indexer->find($ck_query);
        $totalHits = count($ck_hits);

        if ($totalHits > 0) {
            foreach ($ck_hits as $hit) {
                $indexer->delete($hit->id);
            }
            $message = \sprintf('%s docs removed from index file! %s', $totalHits, $snapshot->getId());
            $this->logInfo($message);
        }

        /*
         * |=================================
         * | step 1: Create Individual Doc
         * |
         * |==================================
         */
        $doc = $this->_createDoc($snapshot);
        $indexer->addDocument($doc);

        $message = \sprintf('Index doc for individual %s added', $snapshot->getId());
        $this->logInfo($message);
    }

    private function _createDoc(IndividualSnapshot $snapshot)
    {
        $doc = $this->__createMainPart($snapshot);
        return $doc;
    }

    private function __createMainPart(IndividualSnapshot $snapshot)
    {
        $doc = new Document();
        /*
         * |=================================
         * | Keys
         * |
         * |==================================
         */
        $k = SearchIndexer::INDIVIDUAL_KEY;
        $v1 = \sprintf(SearchIndexer::INDIVIDUAL_KEY_VALUE_FORMAT, $snapshot->getId());
        $doc->addField(Field::keyword($k, $v1));

        /*
         * |=================================
         * | Thumbnail
         * |
         * |==================================
         */

        /*
         * |=================================
         * | UnIndexed Fields
         * |
         * |==================================
         *
         */
        // $doc->addField(Field::unIndexed('id', $snapshot->getId()));
        $doc->addField(Field::unIndexed('company', $snapshot->getCompany()));
        $doc->addField(Field::unIndexed('revisionNo', $snapshot->getRevisionNo()));
        $doc->addField(Field::unIndexed('version', $snapshot->getVersion()));
        $doc->addField(Field::unIndexed('sysNumber', $snapshot->getSysNumber()));
        $doc->addField(Field::unIndexed('token', $snapshot->getToken()));
        $doc->addField(Field::unIndexed('uuid', $snapshot->getUuid()));
        $doc->addField(Field::unIndexed('gender', $snapshot->getGender()));
        $doc->addField(Field::unIndexed('birthday', $snapshot->getBirthday()));
        $doc->addField(Field::unIndexed('lastStatusId', $snapshot->getLastStatusId()));
        $doc->addField(Field::unIndexed('passportIssuePlace', $snapshot->getPassportIssuePlace()));
        $doc->addField(Field::unIndexed('passportIssueDate', $snapshot->getPassportIssueDate()));
        $doc->addField(Field::unIndexed('passportExpiredDate', $snapshot->getPassportExpiredDate()));
        $doc->addField(Field::unIndexed('workPermitNo', $snapshot->getWorkPermitNo()));
        $doc->addField(Field::unIndexed('workPermitDate', $snapshot->getWorkPermitDate()));
        $doc->addField(Field::unIndexed('workPermitExpiredDate', $snapshot->getWorkPermitExpiredDate()));
        $doc->addField(Field::unIndexed('familyBookNo', $snapshot->getFamilyBookNo()));
        $doc->addField(Field::unIndexed('personalIdNumber', $snapshot->getPersonalIdNumber()));
        $doc->addField(Field::unIndexed('nationality', $snapshot->getNationality()));

        // $doc->addField(Field::text('createdOn', $snapshot->getCreatedOn()));
        // $doc->addField(Field::text('lastChangeOn', $snapshot->getLastChangeOn()));
        // $doc->addField(Field::text('createdBy', $snapshot->getCreatedBy()));
        // $doc->addField(Field::text('lastChangeBy', $snapshot->getLastChangeBy()));

        /*
         *
         *
         * |=================================
         * | Keywords Fields
         * |
         * |==================================
         */
        $doc->addField(Field::keyword('employeeStatus', $snapshot->getEmployeeStatus()));
        $doc->addField(Field::keyword('employeeCode', $snapshot->getEmployeeCode()));
        $doc->addField(Field::keyword('passportNo', $snapshot->getPassportNo()));
        $doc->addField(Field::keyword('familyBookNo', $snapshot->getFamilyBookNo()));
        $doc->addField(Field::keyword('ssoNumber', $snapshot->getSsoNumber()));
        $doc->addField(Field::keyword('individualType', $snapshot->getIndividualType()));
        $doc->addField(Field::keyword('ssoNumber', $snapshot->getSsoNumber()));

        /*
         * |=================================
         * | Text Fields
         * |
         * |==================================
         */

        $doc->addField(Field::text('individualName', $snapshot->getIndividualName()));
        $doc->addField(Field::text('individualNameLocal', $snapshot->getIndividualNameLocal()));
        $doc->addField(Field::text('firstName', $snapshot->getFirstName()));
        $doc->addField(Field::text('firstNameLocal', $snapshot->getFirstNameLocal()));
        $doc->addField(Field::text('middleName', $snapshot->getMiddleName()));
        $doc->addField(Field::text('middleNameLocal', $snapshot->getMiddleNameLocal()));
        $doc->addField(Field::text('lastName', $snapshot->getLastName()));
        $doc->addField(Field::text('lastNameLocal', $snapshot->getLastNameLocal()));
        $doc->addField(Field::text('nickName', $snapshot->getNickName()));
        $doc->addField(Field::text('remarks', $snapshot->getRemarks()));

        return $doc;
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
