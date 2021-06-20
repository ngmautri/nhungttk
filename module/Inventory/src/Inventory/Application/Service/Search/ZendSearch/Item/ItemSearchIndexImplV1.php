<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Entity\NmtInventoryItemPicture;
use Application\Entity\NmtInventoryItemSerial;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Collection;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Item\Variant\GenericVariant;
use Inventory\Domain\Service\Search\ItemSearchIndexInterface;
use Webmozart\Assert\Assert;
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
class ItemSearchIndexImplV1 extends AbstractService implements ItemSearchIndexInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchIndexInterface::createIndex()
     */
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

                /*
                 * if ($item->getId() < 5722) {
                 * continue;
                 * }
                 */

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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchIndexInterface::optimizeIndex()
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
     * @see \Inventory\Domain\Service\Search\ItemSearchIndexInterface::createDoc()
     */
    public function createDoc(ItemSnapshot $snapshot)
    {
        try {

            $indexResult = new IndexingResult();

            if (! $snapshot instanceof ItemSnapshot) {
                throw new \InvalidArgumentException("ItemSnapshot not given");
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
     * @param \ZendSearch\Lucene\SearchIndexInterface $indexer
     * @param ItemSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _createIndexForSnapshot(SearchIndexInterface $indexer, ItemSnapshot $snapshot)
    {
        if (! $snapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("ItemSnapshot empty");
        }

        /*
         * |=================================
         * | Delete, if exits
         * |
         * |==================================
         */

        $k = SearchIndexer::ITEM_KEY;
        $v = \sprintf(SearchIndexer::ITEM_KEY_FORMAT, $snapshot->getId());

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
         * | step 1: Create Item Doc
         * |
         * |==================================
         */
        $doc = $this->_createDoc($snapshot);
        $indexer->addDocument($doc);

        $message = \sprintf('Index doc item %s added', $snapshot->getId());
        $this->logInfo($message);

        /*
         * |=================================
         * | step 2: Create Item with Variant, if any
         * |
         * |==================================
         */
        $variantCollection = $snapshot->getVariantCollection();

        if ($variantCollection->count() > 0) {

            // add doc with serial numeber
            foreach ($variantCollection as $variant) {
                $message = \sprintf('Index doc for item-variant %s-%s added', $snapshot->getId(), $variant->getId());
                $this->logInfo($message);

                $doc = $this->_createDocWithVariant($snapshot, $variant);
                $indexer->addDocument($doc);
            }
        }

        /*
         * |=================================
         * | step 3: Create Doc with Serial, if any
         * |
         * |==================================
         */
        $snCollection = $snapshot->getSerialNoList();

        if ($snCollection->count() > 0) {

            // add doc with serial numeber
            foreach ($snCollection as $sn) {
                $message = \sprintf('Index doc for item-serial %s-%s added', $snapshot->getId(), $sn->getId());
                $this->logInfo($message);

                $doc = $this->_createDocWithSerial($snapshot, $sn);
                $indexer->addDocument($doc);
            }
        }
    }

    private function _createDoc(ItemSnapshot $snapshot, $serial = null, $variant = null)
    {
        $doc = $this->__createMainPart($snapshot);
        $doc = $this->__createPartForSerial($doc, $snapshot, $serial);
        $doc = $this->__createPartForVariant($doc, $snapshot, $variant);
        return $doc;
    }

    private function _createDocWithVariant(ItemSnapshot $snapshot, $variant)
    {
        return $this->_createDoc($snapshot, null, $variant);
    }

    private function _createDocWithSerial(ItemSnapshot $snapshot, $serial)
    {
        return $this->_createDoc($snapshot, $serial, null);
    }

    private function __createMainPart(ItemSnapshot $snapshot)
    {
        $doc = new Document();
        /*
         * |=================================
         * | Keys
         * |
         * |==================================
         */
        $k = SearchIndexer::ITEM_KEY;
        $v1 = \sprintf(SearchIndexer::ITEM_KEY_FORMAT, $snapshot->getId());
        $doc->addField(Field::keyword($k, $v1));

        $k = SearchIndexer::FIXED_ASSET_KEY;
        $v = \sprintf(SearchIndexer::FIXED_ASSET_VALUE, $snapshot->getIsFixedAsset());
        $doc->addField(Field::keyword($k, $v));

        $k = SearchIndexer::STOCKED_ITEM_KEY;
        $v = \sprintf(SearchIndexer::STOCKED_ITEM_VALUE, $snapshot->getIsStocked());
        $doc->addField(Field::keyword($k, $v));

        /*
         * |=================================
         * | Thumbnail
         * |
         * |==================================
         */
        $pictureList = $snapshot->getPictureList();

        $thumbnail_file = null;

        if ($pictureList != null) {
            $lastPic = $pictureList->last();

            if ($lastPic instanceof NmtInventoryItemPicture) {
                $thumbnail_file = "/thumbnail/item/" . $lastPic->getFolderRelative() . "thumbnail_200_" . $lastPic->getFileName();
                $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU
            }
        }

        $doc->addField(Field::UnIndexed('item_thumbnail', $thumbnail_file));

        /*
         * |=================================
         * | UnIndexed Fields
         * |
         * |==================================
         */
        $doc->addField(Field::UnIndexed('isFixedAsset', $snapshot->getIsFixedAsset()));
        $doc->addField(Field::UnIndexed('isSparepart', $snapshot->getIsSparepart()));
        $doc->addField(Field::UnIndexed('isModel', $snapshot->getIsModel()));
        $doc->addField(Field::UnIndexed('item_id', $snapshot->getId()));
        $doc->addField(Field::UnIndexed('token', $snapshot->getToken()));
        $doc->addField(Field::UnIndexed('standardUnitName', $snapshot->getStandardUnitName()));

        /*
         * |=================================
         * | Keywords Fields
         * |
         * |==================================
         */
        $doc->addField(Field::keyword('itemSku_key', $snapshot->getItemSku()));
        $doc->addField(Field::keyword('itemSku1_key', $snapshot->getItemSku1()));
        $doc->addField(Field::keyword('itemSku2_key', $snapshot->getItemSku2()));

        // \substr($snapshot->getItemSku(), 0, strpos($snapshot->getItemSku(), "-") + 1);
        $doc->addField(Field::keyword('itemSku_pre', \substr($snapshot->getItemSku(), 0, strpos($snapshot->getItemSku(), "-") + 1)));
        $doc->addField(Field::keyword('itemSku1_pre', \substr($snapshot->getItemSku1(), 0, strpos($snapshot->getItemSku1(), "-") + 1)));
        $doc->addField(Field::keyword('itemSku2_pre', \substr($snapshot->getItemSku2(), 0, strpos($snapshot->getItemSku2(), "-") + 1)));

        $doc->addField(Field::keyword('itemSku_1', preg_replace('/[()]/', '', $snapshot->getItemSku())));
        $doc->addField(Field::keyword('itemSku1_1', preg_replace('/[()]/', '', $snapshot->getItemSku1())));
        $doc->addField(Field::keyword('itemSku2_1', preg_replace('/[()]/', '', $snapshot->getItemSku2())));

        $doc->addField(Field::keyword('barcode', $snapshot->getBarcode()));
        $doc->addField(Field::keyword('barcode39', $snapshot->getBarcode39()));
        $doc->addField(Field::keyword('barcode128', $snapshot->getBarcode128()));

        $doc->addField(Field::keyword('assetLabel', $snapshot->getAssetLabel()));
        $doc->addField(Field::keyword('assetLabel1', $snapshot->getAssetLabel1()));
        $doc->addField(Field::keyword('sysNumber', $snapshot->getSysNumber()));

        /*
         * |=================================
         * | Text Fields
         * |
         * |==================================
         */
        $doc->addField(Field::text('itemSku', $snapshot->getItemSku()));
        $doc->addField(Field::text('itemSku1', $snapshot->getItemSku1()));
        $doc->addField(Field::text('itemSku2', $snapshot->getItemSku2()));

        $doc->addField(Field::text('itemName', $snapshot->getItemName()));
        // $doc->addField(Field::text('itemNameForeign', $snapshot->getItemNameForeign()));
        $doc->addField(Field::text('itemDescription', $snapshot->getItemDescription()));
        $doc->addField(Field::text('keywords', $snapshot->getKeywords()));
        $doc->addField(Field::text('manufacturer', $snapshot->getManufacturer()));
        $doc->addField(Field::text('manufacturerCode', $snapshot->getManufacturerCode()));
        $doc->addField(Field::text('manufacturerCatalog', $snapshot->getManufacturerCatalog()));
        $doc->addField(Field::text('manufacturerModel', $snapshot->getManufacturerModel()));
        $doc->addField(Field::text('manufacturerSerial', $snapshot->getManufacturerSerial()));
        $doc->addField(Field::text('serialNumber', $snapshot->getSerialNumber()));
        $doc->addField(Field::text('itemInternalLabel', $snapshot->getItemInternalLabel()));
        $doc->addField(Field::text('sparepartLabel', $snapshot->getSparepartLabel()));
        $doc->addField(Field::text('remarks', $snapshot->getRemarks()));
        $doc->addField(Field::text('remarksText', $snapshot->getRemarksText()));
        $doc->addField(Field::text('hsCode', $snapshot->getHsCode()));

        return $doc;
    }

    /**
     *
     * @param Document $doc
     * @param ItemSnapshot $snapshot
     * @param GenericVariant $variant
     * @return \ZendSearch\Lucene\Document
     */
    private function __createPartForVariant(Document $doc, ItemSnapshot $snapshot, GenericVariant $variant = null)
    {
        Assert::notNull($doc);

        $k = SearchIndexer::VARIANT_KEY;
        $v = SearchIndexer::YES;

        if ($variant == null) {
            $variant = new GenericVariant(); // create empty variant
            $v = SearchIndexer::NO;
        }

        /*
         * |=================================
         * | Keywords Fields
         * |
         * |==================================
         */
        $doc->addField(Field::keyword($k, $v));

        $doc->addField(Field::keyword('variantCode', $variant->getVariantCode()));
        $doc->addField(Field::keyword('variantSysNumber', $variant->getSysNumber()));

        /*
         * |=================================
         * | UnIndexed Fields
         * |
         * |==================================
         */
        $doc->addField(Field::UnIndexed('variantId', $variant->getId()));

        /*
         * |=================================
         * | Text Fields
         * |
         * |==================================
         */
        $doc->addField(Field::text('variantName', $variant->getCombinedName()));
        $doc->addField(Field::text('variantFullName', $variant->getFullCombinedName()));

        return $doc;
    }

    /**
     *
     * @param Document $doc
     * @param ItemSnapshot $snapshot
     * @param GenericVariant $variant
     * @return \ZendSearch\Lucene\Document
     */
    private function __createPartForSerial(Document $doc, ItemSnapshot $snapshot, NmtInventoryItemSerial $sn = null)
    {
        Assert::notNull($doc);

        $k = SearchIndexer::SERIAL_KEY;
        $v = SearchIndexer::YES;

        if ($sn == null) {
            $sn = new NmtInventoryItemSerial();
            $v = SearchIndexer::NO;
        }

        /*
         * |=================================
         * | Keywords Fields
         * |
         * |==================================
         */
        $doc->addField(Field::keyword($k, $v));
        $doc->addField(Field::keyword('serialSystemNo', $sn->getSysNumber()));

        /*
         * |=================================
         * | UnIndexed Fields
         * |
         * |==================================
         */
        $doc->addField(Field::UnIndexed('serialId', $sn->getId()));

        /*
         * |=================================
         * | Text Fields
         * |
         * |==================================
         */
        $doc->addField(Field::text('serialNo', $sn->getSerialNumber()));

        $serialNo1 = null;
        if ($sn->getSerialNumber() != null) {
            $v = preg_replace('/[-]/', '', \substr($sn->getSerialNumber(), - 5));
            $serialNo1 = $v * 1;
        }
        $doc->addField(Field::text('serialNo1', $serialNo1));

        $doc->addField(Field::text('serialNo2', $sn->getSerialNumber2()));
        $doc->addField(Field::text('serialMfgNumber', $sn->getMfgSerialNumber()));
        $doc->addField(Field::text('serialMfgName', $sn->getMfgName()));
        $doc->addField(Field::text('serialMfgModel', $sn->getMfgModel()));
        $doc->addField(Field::text('serialMfgModel1', $sn->getMfgModel1()));
        $doc->addField(Field::text('serialERPNumber', $sn->getErpAssetNumber()));
        $doc->addField(Field::text('serialLotNumber', $sn->getLotNumber()));
        $doc->addField(Field::text('mfg_description', $sn->getRemarks()));

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

    private function getItemSerialIndexer()
    {
        $indexer = null;
        try {
            $indexer = Lucene::open(getcwd() . SearchIndexer::ITEM_SERIAL_INDEX_PATH);
        } catch (RuntimeException $e) {
            $indexer = Lucene::create(getcwd() . SearchIndexer::ITEM_SERIAL_INDEX_PATH);
        }

        return $indexer;
    }

    /**
     *
     * @deprecated
     * @param \ZendSearch\Lucene\SearchIndexInterface $indexer
     * @param ItemSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _createNewIndexFromSnapshot(SearchIndexInterface $indexer, ItemSnapshot $snapshot)
    {
        if (! $snapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("ItemSnapshot empty");
        }

        $snList = $snapshot->getSerialNoList();

        if ($snList instanceof Collection) {

            if ($snList->count() > 0) {
                // add doc with serial numeber
                foreach ($snList as $sn) {
                    $message = \sprintf('Add doc with serial numeber! %s', \get_class($snList));
                    $this->logInfo($message);

                    $doc = $this->__createDoc($snapshot, $sn);
                    $indexer->addDocument($doc);
                }

                $message = \sprintf('Search index created for item with serial no.! %s', $snapshot->getId());
                $this->logInfo($message);

                return;
            }
        }

        /**
         *
         * @var ItemVariantCollection $variantCollection ;
         */
        $variantCollection = $snapshot->getVariantCollection();

        if ($variantCollection->count() > 0) {
            // add doc with serial numeber
            foreach ($variantCollection as $variant) {
                $message = \sprintf('Add doc with variants! %s', \get_class($snList));
                $this->logInfo($message);

                $doc = $this->__createDocForVariant($snapshot, $variant);
                $indexer->addDocument($doc);
            }

            $message = \sprintf('Search index created for item with serial no.! %s', $snapshot->getId());
            $this->logInfo($message);

            return;
        }

        $doc = $this->__createDoc($snapshot);
        $indexer->addDocument($doc);

        $message = \sprintf('Search index created! %s', $snapshot->getId());
        $this->logInfo($message);
    }
}
