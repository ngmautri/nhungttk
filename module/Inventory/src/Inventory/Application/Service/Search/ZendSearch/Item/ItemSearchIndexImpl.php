<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Entity\NmtInventoryItemPicture;
use Application\Entity\NmtInventoryItemSerial;
use Application\Service\AbstractService;
use Doctrine\Common\Collections\Collection;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Service\Search\ItemSearchIndexInterface;
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
class ItemSearchIndexImpl extends AbstractService implements ItemSearchIndexInterface
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
                $this->_createNewIndexFromSnapshot($indexer, $snapshot);
            }

            $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {

            echo $e->getTraceAsString();

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

            $this->_updateIndexFromSnapshot($indexer, $snapshot);

            $message = \sprintf('Document has been added successfully! %s', $snapshot->getId());

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($indexer, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s', $e->getMessage());
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(False);
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
    private function _createNewIndexFromSnapshot(SearchIndexInterface $indexer, ItemSnapshot $snapshot)
    {
        if (! $snapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("ItemSnapshot empty");
        }

        $snList = $snapshot->getSerialNoList();

        if (! $snList instanceof Collection) {
            return;
        }

        if ($snList->count() == 0) {
            $doc = $this->__createDoc($snapshot);
            $indexer->addDocument($doc);
            return;
        }
        // add doc with serial numeber
        foreach ($snList as $sn) {
            $doc = $this->__createDoc($snapshot, $sn);
            $indexer->addDocument($doc);
        }
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $indexer
     * @param ItemSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _updateIndexFromSnapshot(SearchIndexInterface $indexer, ItemSnapshot $snapshot)
    {
        if (! $snapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("ItemSnapshot empty");
        }

        $k = SearchIndexer::ITEM_KEY;
        $v = \sprintf(SearchIndexer::ITEM_KEY_FORMAT, $snapshot->getId());

        $ck_query = \sprintf('%s:%s', $k, $v);

        $ck_hits = $indexer->find($ck_query);

        if (count($ck_hits) > 0) {
            foreach ($ck_hits as $hit) {
                $indexer->delete($hit->id);
            }
        }

        $this->_createNewIndexFromSnapshot($indexer, $snapshot);
    }

    /**
     *
     * @param ItemSnapshot $snapshot
     * @param NmtInventoryItemSerial $sn
     * @return \ZendSearch\Lucene\Document
     */
    private function __createDoc(ItemSnapshot $snapshot, NmtInventoryItemSerial $sn = null)
    {
        $doc = new Document();

        // KEY

        $k = SearchIndexer::ITEM_KEY;
        $v = \sprintf(SearchIndexer::ITEM_KEY_FORMAT, $snapshot->getId());
        $doc->addField(Field::keyword($k, $v));

        $k = SearchIndexer::FIXED_ASSET_KEY;
        $v = \sprintf(SearchIndexer::FIXED_ASSET_VALUE, $snapshot->getIsFixedAsset());
        $doc->addField(Field::keyword($k, $v));

        $k = SearchIndexer::STOCKED_ITEM_KEY;
        $v = \sprintf(SearchIndexer::STOCKED_ITEM_VALUE, $snapshot->getIsStocked());
        $doc->addField(Field::keyword($k, $v));

        $pictureList = $snapshot->getPictureList();

        $thumbnail_file = null;

        $lastPic = $pictureList->last();

        if ($lastPic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "/thumbnail/item/" . $lastPic->getFolderRelative() . "thumbnail_200_" . $lastPic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU
        }

        $doc->addField(Field::UnIndexed('item_thumbnail', $thumbnail_file));

        // Serial
        if ($sn !== null) {
            $doc->addField(Field::text('serialNo', $sn->getSerialNumber()));

            $v = preg_replace('/[-]/', '', \substr($sn->getSerialNumber(), - 5));
            echo $v . "\n====";
            $doc->addField(Field::text('serialNo1', $v * 1));

            $doc->addField(Field::text('serialNo2', $sn->getSerialNumber2()));
            $doc->addField(Field::text('serialMfgNumber', $sn->getMfgSerialNumber()));
            $doc->addField(Field::text('serialMfgName', $sn->getMfgName()));
            $doc->addField(Field::text('serialMfgModel', $sn->getMfgModel()));
            $doc->addField(Field::text('serialMfgModel1', $sn->getMfgModel1()));
            $doc->addField(Field::text('serialERPNumber', $sn->getErpAssetNumber()));
            $doc->addField(Field::text('serialLotNumber', $sn->getLotNumber()));
            $doc->addField(Field::UnIndexed('serialId', $sn->getId()));
            $doc->addField(Field::keyword('serialSystemNo', $sn->getSysNumber()));
            $doc->addField(Field::text('mfg_description', $sn->getRemarks()));
        } else {

            $doc->addField(Field::UnIndexed('serialNo', null));
            $doc->addField(Field::UnIndexed('serialNo1', null));
            $doc->addField(Field::UnIndexed('serialNo2', null));
            $doc->addField(Field::UnIndexed('serialMfgNumber', null));
            $doc->addField(Field::UnIndexed('serialMfgName', null));
            $doc->addField(Field::UnIndexed('serialMfgModel', null));
            $doc->addField(Field::UnIndexed('serialMfgModel1', null));
            $doc->addField(Field::UnIndexed('serialERPNumber', null));
            $doc->addField(Field::UnIndexed('serialLotNumber', null));
            $doc->addField(Field::UnIndexed('serialId', null));
            $doc->addField(Field::UnIndexed('serialSystemNo', null));
            $doc->addField(Field::UnIndexed('mfg_description', null));
        }

        // Serial END
        // ==========================

        $doc->addField(Field::UnIndexed('isFixedAsset', $snapshot->getIsFixedAsset()));
        $doc->addField(Field::UnIndexed('isSparepart', $snapshot->getIsSparepart()));
        $doc->addField(Field::UnIndexed('isModel', $snapshot->getIsModel()));

        $doc->addField(Field::UnIndexed('item_id', $snapshot->getId()));
        $doc->addField(Field::UnIndexed('token', $snapshot->getToken()));

        $doc->addField(Field::keyword('itemSku_key', $snapshot->getItemSku()));
        $doc->addField(Field::keyword('itemSku1_key', $snapshot->getItemSku1()));
        $doc->addField(Field::keyword('itemSku2_key', $snapshot->getItemSku2()));

        $doc->addField(Field::text('itemSku', $snapshot->getItemSku()));
        $doc->addField(Field::text('itemSku1', $snapshot->getItemSku1()));
        $doc->addField(Field::text('itemSku2', $snapshot->getItemSku2()));

        // \substr($snapshot->getItemSku(), 0, strpos($snapshot->getItemSku(), "-") + 1);
        $doc->addField(Field::keyword('itemSku_pre', \substr($snapshot->getItemSku(), 0, strpos($snapshot->getItemSku(), "-") + 1)));
        $doc->addField(Field::keyword('itemSku1_pre', \substr($snapshot->getItemSku1(), 0, strpos($snapshot->getItemSku1(), "-") + 1)));
        $doc->addField(Field::keyword('itemSku2_pre', \substr($snapshot->getItemSku2(), 0, strpos($snapshot->getItemSku2(), "-") + 1)));

        $doc->addField(Field::keyword('itemSku_1', preg_replace('/[()]/', '', $snapshot->getItemSku())));
        $doc->addField(Field::keyword('itemSku1_1', preg_replace('/[()]/', '', $snapshot->getItemSku1())));
        $doc->addField(Field::keyword('itemSku2_1', preg_replace('/[()]/', '', $snapshot->getItemSku2())));

        $doc->addField(Field::text('itemName', $snapshot->getItemName()));
        // $doc->addField(Field::text('itemNameForeign', $snapshot->getItemNameForeign()));
        $doc->addField(Field::text('itemDescription', $snapshot->getItemDescription()));
        $doc->addField(Field::text('keywords', $snapshot->getKeywords()));

        $doc->addField(Field::keyword('barcode', $snapshot->getBarcode()));
        $doc->addField(Field::keyword('barcode39', $snapshot->getBarcode39()));
        $doc->addField(Field::keyword('barcode128', $snapshot->getBarcode128()));

        $doc->addField(Field::text('manufacturer', $snapshot->getManufacturer()));
        $doc->addField(Field::text('manufacturerCode', $snapshot->getManufacturerCode()));
        $doc->addField(Field::text('manufacturerCatalog', $snapshot->getManufacturerCatalog()));
        $doc->addField(Field::text('manufacturerModel', $snapshot->getManufacturerModel()));
        $doc->addField(Field::text('manufacturerSerial', $snapshot->getManufacturerSerial()));
        $doc->addField(Field::text('serialNumber', $snapshot->getSerialNumber()));
        $doc->addField(Field::text('itemInternalLabel', $snapshot->getItemInternalLabel()));

        $doc->addField(Field::keyword('assetLabel', $snapshot->getAssetLabel()));
        $doc->addField(Field::keyword('assetLabel1', $snapshot->getAssetLabel1()));

        $doc->addField(Field::text('sparepartLabel', $snapshot->getSparepartLabel()));
        $doc->addField(Field::text('remarks', $snapshot->getRemarks()));
        $doc->addField(Field::keyword('sysNumber', $snapshot->getSysNumber()));
        $doc->addField(Field::text('remarksText', $snapshot->getRemarksText()));
        $doc->addField(Field::text('hsCode', $snapshot->getHsCode()));

        /*
         * $doc->addField(Field::text('origin', $snapshot->getOrigin()));
         * $doc->addField(Field::text('serialMfgDate', $snapshot->getSerialMfgDate()));
         * $doc->addField(Field::text('serialWarrantyStartDate', $snapshot->getSerialWarrantyStartDate()));
         * $doc->addField(Field::text('serialWarrantyEndDate', $snapshot->getSerialWarrantyEndDate()));
         * $doc->addField(Field::text('pictureList', $snapshot->getPictureList()));
         * $doc->addField(Field::text('attachmentList', $snapshot->getAttachmentList()));
         * $doc->addField(Field::text('prList', $snapshot->getPrList()));
         * $doc->addField(Field::text('poList', $snapshot->getPoList()));
         * $doc->addField(Field::text('apList', $snapshot->getApList()));
         * $doc->addField(Field::text('serialNoList', $snapshot->getSerialNoList()));
         * $doc->addField(Field::text('batchList', $snapshot->getBatchList()));
         * $doc->addField(Field::text('fifoLayerList', $snapshot->getFifoLayerList()));
         * $doc->addField(Field::text('onHandQty', $snapshot->getOnHandQty()));
         * $doc->addField(Field::text('onHandValue', $snapshot->getOnHandValue()));
         * $doc->addField(Field::text('standardUnitName', $snapshot->getStandardUnitName()));
         * $doc->addField(Field::text('id', $snapshot->getId()));
         * $doc->addField(Field::text('warehouseId', $snapshot->getWarehouseId()));
         * $doc->addField(Field::text('isActive', $snapshot->getIsActive()));
         * $doc->addField(Field::text('isStocked', $snapshot->getIsStocked()));
         * $doc->addField(Field::text('isSaleItem', $snapshot->getIsSaleItem()));
         * $doc->addField(Field::text('isPurchased', $snapshot->getIsPurchased()));
         * $doc->addField(Field::text('isFixedAsset', $snapshot->getIsFixedAsset()));
         * $doc->addField(Field::text('isSparepart', $snapshot->getIsSparepart()));
         * $doc->addField(Field::text('uom', $snapshot->getUom()));
         * $doc->addField(Field::text('status', $snapshot->getStatus()));
         * $doc->addField(Field::text('createdOn', $snapshot->getCreatedOn()));
         * $doc->addField(Field::text('lastPurchasePrice', $snapshot->getLastPurchasePrice()));
         * $doc->addField(Field::text('lastPurchaseCurrency', $snapshot->getLastPurchaseCurrency()));
         * $doc->addField(Field::text('lastPurchaseDate', $snapshot->getLastPurchaseDate()));
         * $doc->addField(Field::text('leadTime', $snapshot->getLeadTime()));
         * $doc->addField(Field::text('validFromDate', $snapshot->getValidFromDate()));
         * $doc->addField(Field::text('validToDate', $snapshot->getValidToDate()));
         * $doc->addField(Field::text('location', $snapshot->getLocation()));
         * $doc->addField(Field::text('itemType', $snapshot->getItemType()));
         * $doc->addField(Field::text('itemCategory', $snapshot->getItemCategory()));
         * $doc->addField(Field::text('localAvailabiliy', $snapshot->getLocalAvailabiliy()));
         * $doc->addField(Field::text('lastChangeOn', $snapshot->getLastChangeOn()));
         * $doc->addField(Field::text('token', $snapshot->getToken()));
         * $doc->addField(Field::text('checksum', $snapshot->getChecksum()));
         * $doc->addField(Field::text('currentState', $snapshot->getCurrentState()));
         * $doc->addField(Field::text('docNumber', $snapshot->getDocNumber()));
         * $doc->addField(Field::text('monitoredBy', $snapshot->getMonitoredBy()));
         * $doc->addField(Field::text('revisionNo', $snapshot->getRevisionNo()));
         * $doc->addField(Field::text('assetGroup', $snapshot->getAssetGroup()));
         * $doc->addField(Field::text('assetClass', $snapshot->getAssetClass()));
         * $doc->addField(Field::text('stockUomConvertFactor', $snapshot->getStockUomConvertFactor()));
         * $doc->addField(Field::text('purchaseUomConvertFactor', $snapshot->getPurchaseUomConvertFactor()));
         * $doc->addField(Field::text('salesUomConvertFactor', $snapshot->getSalesUomConvertFactor())); *
         * $doc->addField(Field::text('capacity', $snapshot->getCapacity()));
         * $doc->addField(Field::text('avgUnitPrice', $snapshot->getAvgUnitPrice()));
         * $doc->addField(Field::text('standardPrice', $snapshot->getStandardPrice()));
         * $doc->addField(Field::text('uuid', $snapshot->getUuid()));
         * $doc->addField(Field::text('createdBy', $snapshot->getCreatedBy()));
         * $doc->addField(Field::text('itemGroup', $snapshot->getItemGroup()));
         * $doc->addField(Field::text('stockUom', $snapshot->getStockUom()));
         * $doc->addField(Field::text('cogsAccount', $snapshot->getCogsAccount()));
         * $doc->addField(Field::text('purchaseUom', $snapshot->getPurchaseUom()));
         * $doc->addField(Field::text('salesUom', $snapshot->getSalesUom()));
         * $doc->addField(Field::text('inventoryAccount', $snapshot->getInventoryAccount()));
         * $doc->addField(Field::text('expenseAccount', $snapshot->getExpenseAccount()));
         * $doc->addField(Field::text('revenueAccount', $snapshot->getRevenueAccount()));
         * $doc->addField(Field::text('defaultWarehouse', $snapshot->getDefaultWarehouse()));
         * $doc->addField(Field::text('lastChangeBy', $snapshot->getLastChangeBy()));
         * $doc->addField(Field::text('standardUom', $snapshot->getStandardUom()));
         * $doc->addField(Field::text('company', $snapshot->getCompany()));
         * $doc->addField(Field::text('lastPrRow', $snapshot->getLastPrRow()));
         * $doc->addField(Field::text('lastPoRow', $snapshot->getLastPoRow()));
         * $doc->addField(Field::text('lastApInvoiceRow', $snapshot->getLastApInvoiceRow()));
         * $doc->addField(Field::text('lastTrxRow', $snapshot->getLastTrxRow()));
         * $doc->addField(Field::text('lastPurchasing', $snapshot->getLastPurchasing()));
         * $doc->addField(Field::text('itemTypeId', $snapshot->getItemTypeId()));
         *
         */
        return $doc;
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
}
