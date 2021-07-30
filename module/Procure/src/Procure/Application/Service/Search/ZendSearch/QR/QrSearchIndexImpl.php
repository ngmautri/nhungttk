<?php
namespace Procure\Application\Service\Search\ZendSearch\QR;

use Application\Application\Service\Search\Contracts\IndexingResult;
use Application\Service\AbstractService;
use Procure\Domain\BaseRowSnapshot;
use Procure\Domain\DocSnapshot;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\QuotationRequest\QRRowSnapshot;
use Procure\Domain\QuotationRequest\QRSnapshot;
use Procure\Domain\Service\Search\QrSearchIndexInterface;
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
class QrSearchIndexImpl extends AbstractService implements QrSearchIndexInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PrSearchIndexInterface::updateIndexRow()
     */
    public function updateIndexRow($rows)
    {
        $indexResult = new IndexingResult();
        $indexResult->setIsSuccess(false);

        $msg = null;

        try {

            if (count($rows) == 0) {
                throw new \InvalidArgumentException("No input not given");
            }

            // take long time
            set_time_limit(1500);

            $index = $this->getIndexer();
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {
                /**
                 *
                 * @var QRRow $row ;
                 */
                $msg = \sprintf("%s-%s-%s", $row->getId(), $row->getItem(), $row->getItemName());
                $this->_createIndexFromRowSnapshot($index, $row, FALSE);
            }

            $message = \sprintf('Index has been updated successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($index, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(TRUE);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s - %s', $e->getTraceAsString(), $msg);
            $indexResult->setMessage($message);
        }

        return $indexResult;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchIndexInterface::createIndex()
     */
    public function createIndex($rows)
    {
        $indexResult = new IndexingResult();
        $indexResult->setIsSuccess(false);
        $msg = null;

        try {

            if (count($rows) == 0) {
                throw new \InvalidArgumentException("No input not given");
            }

            // take long time

            set_time_limit(5500);
            ini_set('memory_limit', '256M');

            $index = Lucene::create(getcwd() . QrSearch::INDEX_PATH);
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {
                /**
                 *
                 * @var QRRow $row ;
                 */
                $msg = \sprintf("%s-%s-%s", $row->getId(), $row->getItem(), $row->getItemName());
                $this->_createIndexFromRowSnapshot($index, $row);
            }

            $message = \sprintf('Index has been created successfully! %s', count($rows));

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($index, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(TRUE);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s - %s', $e->getTraceAsString(), $msg);
            $indexResult->setMessage($message);
        }

        return $indexResult;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchIndexInterface::optimizeIndex()
     */
    public function optimizeIndex()
    {
        $indexResult = new IndexingResult();
        $indexResult->setIsSuccess(False);

        try {
            set_time_limit(1500);

            $index = $this->getIndexer();
            $index->optimize();
            $indexResult = $this->_updateIndexingResult($index, $indexResult);
            $message = \sprintf('Index has been optimzed successfully! %s', '');
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(TRUE);
        } catch (Exception $e) {
            $message = \sprintf('Failed! %s', $e->getMessage());
            $indexResult->setMessage($message);
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
        $msg = null;

        try {

            $indexResult = new IndexingResult();
            $indexResult->setIsSuccess(False);

            if (! $doc instanceof QRSnapshot) {
                throw new \InvalidArgumentException("QRSnapshot not given");
            }

            $rows = $doc->getDocRows();

            if ($rows == null) {
                throw new \InvalidArgumentException("QRSnapshot empty");
            }

            // take long time
            set_time_limit(1500);

            $index = $this->getIndexer();
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($rows as $row) {

                if (! $row instanceof QRRow) {
                    return;
                }
                /**
                 *
                 * @var QRRow $row ;
                 */
                $msg = \sprintf("%s-%s-%s", $row->getId(), $row->getItem(), $row->getItemName());

                $this->_createIndexFromRowSnapshot($index, $row->makeSnapshot());
            }

            $message = \sprintf('Document has been added successfully! %s', $doc->getId());

            $indexResult = new IndexingResult();
            $this->_updateIndexingResult($index, $indexResult);
            $indexResult->setMessage($message);
            $indexResult->setIsSuccess(True);
            // $this->getLogger()->info($message);
        } catch (Exception $e) {
            $this->getLogger()->error($e->getTraceAsString());
            $message = \sprintf('Failed! %s-%s', $e->getMessage(), $msg);
            $indexResult->setMessage($message);
        }

        return $indexResult;
    }

    private function _updateIndexingResult($index, IndexingResult $indexResult)
    {
        $indexResult->setDocsCount($index->numDocs());
        $indexResult->setIndexSize($index->count());
        $indexResult->setIndexVesion($index->getFormatVersion());

        if ($index->getDirectory() !== null) {
            $indexResult->setFileList($index->getDirectory()
                ->fileList());
        }
        $indexResult->setIndexDirectory(QrSearch::INDEX_PATH);
        return $indexResult;
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param BaseRowSnapshot $row
     * @throws \InvalidArgumentException
     */
    private function _createIndexFromRowSnapshot(\ZendSearch\Lucene\SearchIndexInterface $index, BaseRowSnapshot $row, $isNew = TRUE)
    {
        $doc = new Document();

        if (! $row instanceof QRRowSnapshot) {
            throw new \InvalidArgumentException("QRRowSnapshot not given!");
        }

        // Deleting old document when updating.
        if (! $isNew) {
            $ck_query = \sprintf('qoute_doc_row_key:%s', \sprintf('%s_%s', $row->getDocId(), $row->getId()));

            $ck_hits = $index->find($ck_query);
            echo (count($ck_hits));

            if (count($ck_hits) > 0) {

                foreach ($ck_hits as $hit) {
                    $index->delete($hit->id);
                }
            }
        }

        /*
         * $doc->addField(Field::text('draftGrQuantity', $row->getDraftGrQuantity()));
         * $doc->addField(Field::text('postedGrQuantity', $row->getPostedGrQuantity()));
         * $doc->addField(Field::text('confirmedGrBalance', $row->getConfirmedGrBalance()));
         * $doc->addField(Field::text('openGrBalance', $row->getOpenGrBalance()));
         * $doc->addField(Field::text('draftAPQuantity', $row->getDraftAPQuantity()));
         * $doc->addField(Field::text('postedAPQuantity', $row->getPostedAPQuantity()));
         * $doc->addField(Field::text('openAPQuantity', $row->getOpenAPQuantity()));
         * $doc->addField(Field::text('billedAmount', $row->getBilledAmount()));
         * $doc->addField(Field::text('openAPAmount', $row->getOpenAPAmount()));
         *
         * $doc->addField(Field::text('companyName', $row->getCompanyName()));
         * $doc->addField(Field::text('vendorName', $row->getVendorName()));
         * $doc->addField(Field::text('vendorCountry', $row->getVendorCountry()));
         * $doc->addField(Field::text('docCurrencyISO', $row->getDocCurrencyISO()));
         * $doc->addField(Field::text('localCurrencyISO', $row->getLocalCurrencyISO()));
         * $doc->addField(Field::text('docCurrencyId', $row->getDocCurrencyId()));
         * $doc->addField(Field::text('localCurrencyId', $row->getLocalCurrencyId()));
         * $doc->addField(Field::text('exchangeRate', $row->getExchangeRate()));
         * $doc->addField(Field::text('docDate', $row->getDocDate()));
         * $doc->addField(Field::text('docYear', $row->getDocYear()));
         * $doc->addField(Field::text('docMonth', $row->getDocMonth()));
         * $doc->addField(Field::text('docWarehouseName', $row->getDocWarehouseName()));
         * $doc->addField(Field::text('docWarehouseCode', $row->getDocWarehouseCode()));
         * $doc->addField(Field::text('warehouseName', $row->getWarehouseName()));
         * $doc->addField(Field::text('warehouseCode', $row->getWarehouseCode()));
         * $doc->addField(Field::text('docUomName', $row->getDocUomName()));
         * $doc->addField(Field::text('docUomCode', $row->getDocUomCode()));
         * $doc->addField(Field::text('docUomDescription', $row->getDocUomDescription()));
         * $doc->addField(Field::text('itemChecksum', $row->getItemChecksum()));
         * $doc->addField(Field::text('itemStandardUnit', $row->getItemStandardUnit())); *
         * $doc->addField(Field::text('itemStandardUnitCode', $row->getItemStandardUnitCode()));
         * $doc->addField(Field::text('itemMonitorMethod', $row->getItemMonitorMethod()));
         * $doc->addField(Field::text('pr', $row->getPr()));
         * $doc->addField(Field::text('prToken', $row->getPrToken()));
         * $doc->addField(Field::text('prChecksum', $row->getPrChecksum()));
         * $doc->addField(Field::text('prNumber', $row->getPrNumber()));
         * $doc->addField(Field::text('prSysNumber', $row->getPrSysNumber()));
         * $doc->addField(Field::text('prRowIndentifer', $row->getPrRowIndentifer()));
         * $doc->addField(Field::text('prRowCode', $row->getPrRowCode()));
         * $doc->addField(Field::text('prRowName', $row->getPrRowName()));
         * $doc->addField(Field::text('prRowConvertFactor', $row->getPrRowConvertFactor()));
         * $doc->addField(Field::text('prRowUnit', $row->getPrRowUnit()));
         * $doc->addField(Field::text('prRowVersion', $row->getPrRowVersion()));
         * $doc->addField(Field::text('projectId', $row->getProjectId()));
         * $doc->addField(Field::text('projectToken', $row->getProjectToken()));
         * $doc->addField(Field::text('projectName', $row->getProjectName()));
         * $doc->addField(Field::text('createdByName', $row->getCreatedByName()));
         * $doc->addField(Field::text('lastChangeByName', $row->getLastChangeByName()));
         * $doc->addField(Field::text('glAccountName', $row->getGlAccountName()));
         * $doc->addField(Field::text('glAccountNumber', $row->getGlAccountNumber()));
         * $doc->addField(Field::text('glAccountType', $row->getGlAccountType()));
         * $doc->addField(Field::text('costCenterName', $row->getCostCenterName()));
         * $doc->addField(Field::text('discountAmount', $row->getDiscountAmount()));
         * $doc->addField(Field::text('id', $row->getId()));
         * $doc->addField(Field::UnIndexed('rowNumber', $row->getRowNumber()));
         * $doc->addField(Field::text('conversionFactor', $row->getConversionFactor()));
         * $doc->addField(Field::text('converstionText', $row->getConverstionText()));
         * $doc->addField(Field::text('taxRate', $row->getTaxRate()));
         * $doc->addField(Field::text('lastchangeOn', $row->getLastchangeOn()));
         * $doc->addField(Field::text('currentState', $row->getCurrentState()));
         * $doc->addField(Field::text('createdOn', $row->getCreatedOn()));
         * $doc->addField(Field::text('traceStock', $row->getTraceStock()));
         * $doc->addField(Field::text('grossAmount', $row->getGrossAmount()));
         * $doc->addField(Field::text('taxAmount', $row->getTaxAmount()));
         * $doc->addField(Field::text('discountRate', $row->getDiscountRate()));
         * $doc->addField(Field::text('revisionNo', $row->getRevisionNo()));
         * $doc->addField(Field::text('targetObject', $row->getTargetObject()));
         * $doc->addField(Field::text('sourceObject', $row->getSourceObject()));
         * $doc->addField(Field::text('targetObjectId', $row->getTargetObjectId()));
         * $doc->addField(Field::text('sourceObjectId', $row->getSourceObjectId()));
         * $doc->addField(Field::text('invoice', $row->getInvoice()));
         * $doc->addField(Field::text('lastchangeBy', $row->getLastchangeBy()));
         * $doc->addField(Field::text('QRRow', $row->getPrRow()));
         * $doc->addField(Field::text('createdBy', $row->getCreatedBy()));
         * $doc->addField(Field::text('warehouse', $row->getWarehouse()));
         * $doc->addField(Field::text('po', $row->getPo()));
         * $doc->addField(Field::text('workflowStatus', $row->getWorkflowStatus()));
         * $doc->addField(Field::text('transactionStatus', $row->getTransactionStatus()));
         * $doc->addField(Field::text('isPosted', $row->getIsPosted()));
         * $doc->addField(Field::text('isDraft', $row->getIsDraft()));
         * $doc->addField(Field::text('exwUnitPrice', $row->getExwUnitPrice()));
         * $doc->addField(Field::text('totalExwPrice', $row->getTotalExwPrice()));
         * $doc->addField(Field::text('convertFactorPurchase', $row->getConvertFactorPurchase()));
         * $doc->addField(Field::text('convertedPurchaseQuantity', $row->getConvertedPurchaseQuantity()));
         * $doc->addField(Field::text('convertedStandardQuantity', $row->getConvertedStandardQuantity()));
         * $doc->addField(Field::text('convertedStockQuantity', $row->getConvertedStockQuantity()));
         * $doc->addField(Field::text('convertedStandardUnitPrice', $row->getConvertedStandardUnitPrice()));
         * $doc->addField(Field::text('convertedStockUnitPrice', $row->getConvertedStockUnitPrice()));
         * $doc->addField(Field::text('convertedPurchaseUnitPrice', $row->getConvertedPurchaseUnitPrice()));
         * $doc->addField(Field::text('localUnitPrice', $row->getLocalUnitPrice()));
         * $doc->addField(Field::text('exwCurrency', $row->getExwCurrency()));
         * $doc->addField(Field::text('localNetAmount', $row->getLocalNetAmount()));
         * $doc->addField(Field::text('localGrossAmount', $row->getLocalGrossAmount()));
         * $doc->addField(Field::text('transactionType', $row->getTransactionType()));
         * $doc->addField(Field::text('isReversed', $row->getIsReversed()));
         * $doc->addField(Field::text('reversalDate', $row->getReversalDate()));
         * $doc->addField(Field::text('standardConvertFactor', $row->getStandardConvertFactor()));
         * $doc->addField(Field::text('isActive', $row->getIsActive()));
         *
         * $doc->addField(Field::text('reversalBlocked', $row->getReversalBlocked()));
         * $doc->addField(Field::text('docUom', $row->getDocUom()));
         * $doc->addField(Field::text('docVersion', $row->getDocVersion()));
         * $doc->addField(Field::text('uuid', $row->getUuid()));
         *
         *
         */

        // IMPORTANT
        $doc->addField(Field::Keyword('qoute_doc_row_key', \sprintf('%s_%s', $row->getDocId(), $row->getId())));
        $doc->addField(Field::UnIndexed('isActive', $row->getIsActive()));

        // Company
        $doc->addField(Field::UnIndexed('companyId', $row->getCompanyId()));
        $doc->addField(Field::UnIndexed('companyToken', $row->getCompanyToken()));

        // vendor
        $doc->addField(Field::UnIndexed('vendor_id', $row->getVendorId()));
        $doc->addField(Field::UnIndexed('vendor_token', $row->getVendorToken()));
        $doc->addField(Field::Keyword('vendor_id_key', \sprintf('vendor_id_key_%s', $row->getVendorId())));

        $doc->addField(Field::text('docNumber', $row->getDocNumber()));
        $doc->addField(Field::keyword('docSysNumber', $row->getDocSysNumber()));

        $doc->addField(Field::UnIndexed('docToken', $row->getDocToken()));
        $doc->addField(Field::UnIndexed('docId', $row->getDocId()));

        $doc->addField(Field::UnIndexed('rowToken', $row->getToken()));
        $doc->addField(Field::UnIndexed('rowId', $row->getId()));

        // PR
        $doc->addField(Field::unIndexed('prRow', $row->getPrRow()));
        $doc->addField(Field::unIndexed('pr', $row->getPr()));
        $doc->addField(Field::unIndexed('prToken', $row->getPrToken()));

        $doc->addField(Field::UnIndexed('itemId', $row->getItem()));
        $doc->addField(Field::UnIndexed('itemToken', $row->getItemToken()));
        $doc->addField(Field::text('itemName', $row->getItemName()));

        // $output = iconv("UTF-8", "ASCII//IGNORE", $row->getItemName1());
        // $doc->addField(Field::text('itemName1', $output));
        // $doc->addField(Field::text('itemName1', $row->getItemName1()));

        $doc->addField(Field::keyword('itemSKU', $row->getItemSKU()));
        $doc->addField(Field::keyword('itemSKU1', $row->getItemSKU1()));
        $doc->addField(Field::keyword('itemSKU2', $row->getItemSKU2()));
        $doc->addField(Field::UnIndexed('itemUUID', $row->getItemUUID()));
        $doc->addField(Field::keyword('itemSysNumber', $row->getItemSysNumber()));

        $doc->addField(Field::UnIndexed('itemVersion', $row->getItemVersion()));
        $doc->addField(Field::UnIndexed('isInventoryItem', $row->getIsInventoryItem()));
        $doc->addField(Field::UnIndexed('isFixedAsset', $row->getIsFixedAsset()));

        $doc->addField(Field::text('itemModel', $row->getItemModel()));
        $doc->addField(Field::text('itemSerial', $row->getItemSerial()));
        $doc->addField(Field::text('itemDefaultManufacturer', $row->getItemDefaultManufacturer()));
        $doc->addField(Field::text('itemManufacturerModel', $row->getItemManufacturerModel()));
        $doc->addField(Field::text('itemManufacturerSerial', $row->getItemManufacturerSerial()));
        $doc->addField(Field::text('itemManufacturerCode', $row->getItemManufacturerCode()));
        $doc->addField(Field::text('itemKeywords', $row->getItemKeywords()));
        $doc->addField(Field::UnIndexed('itemStandardUnitName', $row->getItemStandardUnitName()));
        $doc->addField(Field::UnIndexed('convertedStandardQuantity', $row->getConvertedStandardQuantity()));

        $output = iconv("UTF-8", "ASCII//IGNORE", $row->getItemDescription());
        $doc->addField(Field::text('itemDescription', $output));

        $doc->addField(Field::keyword('itemAssetLabel', $row->getItemAssetLabel()));
        $doc->addField(Field::keyword('itemAssetLabel1', $row->getItemAssetLabel1()));

        $doc->addField(Field::UnIndexed('itemInventoryGL', $row->getItemInventoryGL()));
        $doc->addField(Field::UnIndexed('itemCogsGL', $row->getItemCogsGL()));
        $doc->addField(Field::UnIndexed('itemCostCenter', $row->getItemCostCenter()));

        $doc->addField(Field::UnIndexed('warehouse', $row->getWarehouse()));
        $doc->addField(Field::UnIndexed('warehouseName', $row->getWarehouseName()));

        $doc->addField(Field::UnIndexed('token', $row->getToken()));
        $doc->addField(Field::UnIndexed('quantity', $row->getQuantity()));
        $doc->addField(Field::UnIndexed('unitPrice', $row->getUnitPrice()));
        $doc->addField(Field::UnIndexed('netAmount', $row->getNetAmount()));
        $doc->addField(Field::UnIndexed('unit', $row->getUnit()));
        $doc->addField(Field::UnIndexed('itemUnit', $row->getItemUnit()));

        $doc->addField(Field::text('remarks', $row->getRemarks()));
        $doc->addField(Field::text('vendorItemCode', $row->getVendorItemCode()));
        $doc->addField(Field::text('faRemarks', $row->getFaRemarks()));

        $doc->addField(Field::keyword('rowIdentifer', $row->getRowIdentifer()));
        $doc->addField(Field::keyword('docStatus', $row->getDocStatus()));

        $doc->addField(Field::unIndexed('docQuantity', $row->getDocQuantity()));
        $doc->addField(Field::unIndexed('docUnit', $row->getDocUnit()));
        $doc->addField(Field::unIndexed('docUnitPrice', $row->getDocUnitPrice()));
        $doc->addField(Field::unIndexed('docType', $row->getDocType()));

        $output = iconv("UTF-8", "ASCII//IGNORE", $row->getDescriptionText());
        $doc->addField(Field::text('descriptionText', $output));
        $doc->addField(Field::text('vendorItemName', $row->getVendorItemName()));

        $this->logInfo($row->getRowIdentifer());

        $index->addDocument($doc);
    }

    private function getIndexer()
    {
        $indexer = null;
        try {
            $indexer = Lucene::open(getcwd() . QrSearch::INDEX_PATH);
        } catch (RuntimeException $e) {
            $indexer = Lucene::create(getcwd() . QrSearch::INDEX_PATH);
        }

        return $indexer;
    }
}
