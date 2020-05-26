<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Service\AbstractService;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Service\Search\ItemSearchIndexInterface;
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

        try {

            if (count($rows) == 0) {
                throw new \InvalidArgumentException("No input not given");
            }

            // take long time
            set_time_limit(3500);

            $index = Lucene::create(getcwd() . SearchIndexer::INDEX_PATH);
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {
                $this->_createIndexFromRowSnapshot($index, $row);
            }

            $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($index, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
        } catch (Exception $e) {

            echo $e->getTraceAsString();

            $message = \sprintf('Failed! %s - %s', $e->getMessage(), $row->getId());

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
    public function createDoc(ItemSnapshot $doc)
    {
        try {

            $indexResult = new IndexingResult();

            if (! $doc instanceof ItemSnapshot) {
                throw new \InvalidArgumentException("ItemSnapshot not given");
            }

            // take long time
            set_time_limit(1500);

            $index = $this->getIndexer();
            Analyzer::setDefault(new CaseInsensitive());

            $this->_createDoc($index, $doc);

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

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param IndexingResult $indexResult
     * @return \Application\Application\Service\Search\Contracts\IndexingResult
     */
    private function _updateIndexingResult(\ZendSearch\Lucene\SearchIndexInterface $index, IndexingResult $indexResult)
    {
        $indexResult->setDocsCount($index->numDocs());
        $indexResult->setIndexSize($index->count());
        $indexResult->setIndexVesion($index->getFormatVersion());

        if ($index->getDirectory() !== null) {
            $indexResult->setFileList($index->getDirectory()
                ->fileList());
        }
        $indexResult->setIndexDirectory(SearchIndexer::INDEX_PATH);
        return $indexResult;
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param ItemSnapshot $snapshot
     * @throws \InvalidArgumentException
     */
    private function _createIndexFromSnapshot(\ZendSearch\Lucene\SearchIndexInterface $index, ItemSnapshot $snapshot)
    {
        $doc = new Document();

        if (! $snapshot instanceof ItemSnapshot) {
            throw new \InvalidArgumentException("ItemSnapshot empty");
        }

        $ck_query = \sprintf('item_id:%s', \sprintf('%s', $snapshot->getId()));

        $ck_hits = $index->find($ck_query);
        echo (count($ck_hits));

        if (count($ck_hits) > 0) {

            foreach ($ck_hits as $hit) {
                $index->delete($hit->id);
            }
        }

        $doc->addField(Field::UnIndexed('itemId', $snapshot->getId()));

        $format = \sprintf(SearchIndexer::FIXED_ASSET_VALUE);
        $doc->addField(Field::keyword(SearchIndexer::FIXED_ASSET_KEY, \sprintf($format, $snapshot->getIsFixedAsset())));

        $format = \sprintf(SearchIndexer::STOCKED_ITEM_VALUE);
        $doc->addField(Field::keyword(SearchIndexer::STOCKED_ITEM_KEY, \sprintf($format, $snapshot->getIsSparepart())));

        // Serial
        $doc->addField(Field::text('serialNo', $snapshot->getSerialNo()));
        $doc->addField(Field::text('serialNo1', $snapshot->getSerialNo1()));
        $doc->addField(Field::text('serialNo2', $snapshot->getSerialNo2()));
        $doc->addField(Field::text('serialMfgNumber', $snapshot->getSerialMfgNumber()));
        $doc->addField(Field::text('serialMfgName', $snapshot->getSerialMfgName()));
        $doc->addField(Field::text('serialMfgName1', $snapshot->getSerialMfgName1()));
        $doc->addField(Field::text('serialMfgModel', $snapshot->getSerialMfgModel()));
        $doc->addField(Field::text('serialMfgModel1', $snapshot->getSerialMfgModel1()));
        $doc->addField(Field::text('serialERPNumber', $snapshot->getSerialERPNumber()));
        $doc->addField(Field::text('serialERPNumber1', $snapshot->getSerialERPNumber1()));
        $doc->addField(Field::text('serialLotNumber', $snapshot->getSerialLotNumber()));
        $doc->addField(Field::UnIndexed('serialId', $snapshot->getSerialId()));

        $doc->addField(Field::keyword('serialSystemNo', $snapshot->getSerialSystemNo()));
        $doc->addField(Field::keyword('itemSku', $snapshot->getItemSku()));
        $doc->addField(Field::keyword('itemSku1', $snapshot->getItemSku1()));
        $doc->addField(Field::keyword('itemSku2', $snapshot->getItemSku2()));

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
         *
         * $doc->addField(Field::text('isActive', $snapshot->getIsActive()));
         *
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

        $index->addDocument($doc);
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
