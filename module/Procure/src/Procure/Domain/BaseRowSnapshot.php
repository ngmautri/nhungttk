<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractDTO;

/**
 * Row Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseRowSnapshot extends AbstractDTO
{

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

    public $itemMonitorMethod;

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

    public $projectId;

    public $projectToken;

    public $projectName;

    public $createdByName;

    public $lastChangeByName;

    public $glAccountName;

    public $glAccountNumber;

    public $glAccountType;

    public $costCenterName;

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

    public $standardConvertFactor;

    /**
     *
     * @return mixed
     */
    protected function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @return mixed
     */
    protected function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocSysNumber()
    {
        return $this->docSysNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    protected function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    protected function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocToken()
    {
        return $this->docToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocId()
    {
        return $this->docId;
    }

    /**
     *
     * @return mixed
     */
    protected function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocWarehouseName()
    {
        return $this->docWarehouseName;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocWarehouseCode()
    {
        return $this->docWarehouseCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     *
     * @return mixed
     */
    protected function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUomName()
    {
        return $this->docUomName;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUomCode()
    {
        return $this->docUomCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUomDescription()
    {
        return $this->docUomDescription;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemChecksum()
    {
        return $this->itemChecksum;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemName()
    {
        return $this->itemName;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemName1()
    {
        return $this->itemName1;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemStandardUnitCode()
    {
        return $this->itemStandardUnitCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsInventoryItem()
    {
        return $this->isInventoryItem;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemMonitorMethod()
    {
        return $this->itemMonitorMethod;
    }

    /**
     *
     * @return mixed
     */
    protected function getPr()
    {
        return $this->pr;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrToken()
    {
        return $this->prToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrChecksum()
    {
        return $this->prChecksum;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRowVersion()
    {
        return $this->prRowVersion;
    }

    /**
     *
     * @return mixed
     */
    protected function getProjectId()
    {
        return $this->projectId;
    }

    /**
     *
     * @return mixed
     */
    protected function getProjectToken()
    {
        return $this->projectToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getProjectName()
    {
        return $this->projectName;
    }

    /**
     *
     * @return mixed
     */
    protected function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     *
     * @return mixed
     */
    protected function getLastChangeByName()
    {
        return $this->lastChangeByName;
    }

    /**
     *
     * @return mixed
     */
    protected function getGlAccountName()
    {
        return $this->glAccountName;
    }

    /**
     *
     * @return mixed
     */
    protected function getGlAccountNumber()
    {
        return $this->glAccountNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getGlAccountType()
    {
        return $this->glAccountType;
    }

    /**
     *
     * @return mixed
     */
    protected function getCostCenterName()
    {
        return $this->costCenterName;
    }

    /**
     *
     * @return mixed
     */
    protected function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    protected function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    protected function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @return mixed
     */
    protected function getUnit()
    {
        return $this->unit;
    }

    /**
     *
     * @return mixed
     */
    protected function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     *
     * @return mixed
     */
    protected function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     *
     * @return mixed
     */
    protected function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     *
     * @return mixed
     */
    protected function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     *
     * @return mixed
     */
    protected function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    protected function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    protected function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     *
     * @return mixed
     */
    protected function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     *
     * @return mixed
     */
    protected function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @return mixed
     */
    protected function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @return mixed
     */
    protected function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     *
     * @return mixed
     */
    protected function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     *
     * @return mixed
     */
    protected function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     *
     * @return mixed
     */
    protected function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    protected function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     *
     * @return mixed
     */
    protected function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     *
     * @return mixed
     */
    protected function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     *
     * @return mixed
     */
    protected function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     *
     * @return mixed
     */
    protected function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     *
     * @return mixed
     */
    protected function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     *
     * @return mixed
     */
    protected function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocType()
    {
        return $this->docType;
    }

    /**
     *
     * @return mixed
     */
    protected function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     *
     * @return mixed
     */
    protected function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     *
     * @return mixed
     */
    protected function getInvoice()
    {
        return $this->invoice;
    }

    /**
     *
     * @return mixed
     */
    protected function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrRow()
    {
        return $this->prRow;
    }

    /**
     *
     * @return mixed
     */
    protected function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    protected function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     *
     * @return mixed
     */
    protected function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @return mixed
     */
    protected function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocUom()
    {
        return $this->docUom;
    }

    /**
     *
     * @return mixed
     */
    protected function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return mixed
     */
    protected function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    protected function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    protected function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     *
     * @return mixed
     */
    protected function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     *
     * @return mixed
     */
    protected function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     *
     * @return mixed
     */
    protected function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @return mixed
     */
    protected function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     *
     * @return mixed
     */
    protected function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     *
     * @return mixed
     */
    protected function getStandardConvertFactor()
    {
        return $this->standardConvertFactor;
    }
}
