<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowSnapshot extends AbstractDTO
{

    public $draftGrQuantity;

    public $postedGrQuantity;

    public $confirmedGrBalance;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    public $billedAmount;

    public $openAPAmount;

    public $companyId;

    public $companyToken;

    public $companyName;

    public $vendorId;

    public $vendorToken;

    public $vendorName;

    public $vendorCountry;

    public $docNumber;

    public $docSysNumber;

    public $docCurrencyISO;

    public $localCurrencyISO;

    public $docCurrencyId;

    public $localCurrencyId;

    public $docToken;

    public $docId;

    public $exchangeRate;

    public $docWarehouseName;

    public $docWarehouseCode;

    public $warehouseName;

    public $warehouseCode;

    public $docUomName;

    public $docUomCode;

    public $docUomDescription;

    public $itemToken;

    public $itemChecksum;

    public $itemName;

    public $itemName1;

    public $itemSKU;

    public $itemSKU1;

    public $itemSKU2;

    public $itemUUID;

    public $itemSysNumber;

    public $itemStandardUnit;

    public $itemStandardUnitName;

    public $itemStandardUnitCode;

    public $itemVersion;

    public $isInventoryItem;

    public $isFixedAsset;

    public $pr;

    public $prToken;

    public $prChecksum;

    public $prNumber;

    public $prSysNumber;

    public $prRowIndentifer;

    public $prRowCode;

    public $prRowName;

    public $prRowConvertFactor;

    public $prRowUnit;

    public $prRowVersion;

    public $createdByName;

    public $lastChangeByName;

    public $id;

    public $rowNumber;

    public $token;

    public $quantity;

    public $unitPrice;

    public $netAmount;

    public $unit;

    public $itemUnit;

    public $conversionFactor;

    public $converstionText;

    public $taxRate;

    public $remarks;

    public $isActive;

    public $createdOn;

    public $lastchangeOn;

    public $currentState;

    public $vendorItemCode;

    public $traceStock;

    public $grossAmount;

    public $taxAmount;

    public $faRemarks;

    public $rowIdentifer;

    public $discountRate;

    public $revisionNo;

    public $targetObject;

    public $sourceObject;

    public $targetObjectId;

    public $sourceObjectId;

    public $docStatus;

    public $workflowStatus;

    public $transactionStatus;

    public $isPosted;

    public $isDraft;

    public $exwUnitPrice;

    public $totalExwPrice;

    public $convertFactorPurchase;

    public $convertedPurchaseQuantity;

    public $convertedStandardQuantity;

    public $convertedStockQuantity;

    public $convertedStandardUnitPrice;

    public $convertedStockUnitPrice;

    public $docQuantity;

    public $docUnit;

    public $docUnitPrice;

    public $convertedPurchaseUnitPrice;

    public $docType;

    public $descriptionText;

    public $vendorItemName;

    public $reversalBlocked;

    public $invoice;

    public $lastchangeBy;

    public $prRow;

    public $createdBy;

    public $warehouse;

    public $po;

    public $item;

    public $docUom;

    public $docVersion;

    public $uuid;

    public $localUnitPrice;

    public $exwCurrency;

    public $localNetAmount;

    public $localGrossAmount;

    public $transactionType;

    public $isReversed;

    public $reversalDate;

    public $glAccount;

    public $costCenter;
    /**
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return mixed
     */
    public function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * @return mixed
     */
    public function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * @return mixed
     */
    public function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     * @return mixed
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     * @return mixed
     */
    public function getDocSysNumber()
    {
        return $this->docSysNumber;
    }

    /**
     * @return mixed
     */
    public function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     * @return mixed
     */
    public function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     * @return mixed
     */
    public function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     * @return mixed
     */
    public function getDocToken()
    {
        return $this->docToken;
    }

    /**
     * @return mixed
     */
    public function getDocId()
    {
        return $this->docId;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @return mixed
     */
    public function getDocWarehouseName()
    {
        return $this->docWarehouseName;
    }

    /**
     * @return mixed
     */
    public function getDocWarehouseCode()
    {
        return $this->docWarehouseCode;
    }

    /**
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     * @return mixed
     */
    public function getDocUomName()
    {
        return $this->docUomName;
    }

    /**
     * @return mixed
     */
    public function getDocUomCode()
    {
        return $this->docUomCode;
    }

    /**
     * @return mixed
     */
    public function getDocUomDescription()
    {
        return $this->docUomDescription;
    }

    /**
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     * @return mixed
     */
    public function getItemChecksum()
    {
        return $this->itemChecksum;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @return mixed
     */
    public function getItemName1()
    {
        return $this->itemName1;
    }

    /**
     * @return mixed
     */
    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     * @return mixed
     */
    public function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     * @return mixed
     */
    public function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     * @return mixed
     */
    public function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     * @return mixed
     */
    public function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     * @return mixed
     */
    public function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     * @return mixed
     */
    public function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     * @return mixed
     */
    public function getItemStandardUnitCode()
    {
        return $this->itemStandardUnitCode;
    }

    /**
     * @return mixed
     */
    public function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     * @return mixed
     */
    public function getIsInventoryItem()
    {
        return $this->isInventoryItem;
    }

    /**
     * @return mixed
     */
    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * @return mixed
     */
    public function getPrToken()
    {
        return $this->prToken;
    }

    /**
     * @return mixed
     */
    public function getPrChecksum()
    {
        return $this->prChecksum;
    }

    /**
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     * @return mixed
     */
    public function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     * @return mixed
     */
    public function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     * @return mixed
     */
    public function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     * @return mixed
     */
    public function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     * @return mixed
     */
    public function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     * @return mixed
     */
    public function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     * @return mixed
     */
    public function getPrRowVersion()
    {
        return $this->prRowVersion;
    }

    /**
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     * @return mixed
     */
    public function getLastChangeByName()
    {
        return $this->lastChangeByName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * @return mixed
     */
    public function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @return mixed
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     * @return mixed
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @return mixed
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * @return mixed
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     * @return mixed
     */
    public function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     * @return mixed
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * @return mixed
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * @return mixed
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return mixed
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     * @return mixed
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     * @return mixed
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     * @return mixed
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     * @return mixed
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     * @return mixed
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     * @return mixed
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * @return mixed
     */
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     * @return mixed
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     * @return mixed
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     * @return mixed
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     * @return mixed
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

}