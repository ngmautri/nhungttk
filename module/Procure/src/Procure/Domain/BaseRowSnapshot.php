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

    /*
     * |=============================
     * | Procure\Domain\GenericRow
     * |
     * |=============================
     */
    public $exculdedProps;

    public $docUomVO;

    public $itemStandardUomVO;

    public $uomPairVO;

    public $docQuantityVO;

    public $itemStandardQuantityVO;

    public $docCurrencyVO;

    public $localCurrencyVO;

    public $currencyPair;

    public $docUnitPriceVO;

    public $docItemStandardUnitPriceVO;

    public $docNetAmountVO;

    public $docTaxAmountVO;

    public $docGrossAmountVO;

    public $localUnitPriceVO;

    public $localItemStandardUnitPriceVO;

    public $LocalNetAmountVO;

    public $localTaxAmountVO;

    public $localGrossAmountVO;

    /*
     * |=============================
     * | Procure\Domain\BaseRow
     * |
     * |=============================
     */
    public $standardQuantity;

    public $standardUnitPriceInDocCurrency;

    public $standardUnitPriceInLocCurrency;

    public $docQuantityObject;

    public $baseUomPair;

    public $docUnitPriceObject;

    public $baseDocUnitPriceObject;

    public $localUnitPriceObject;

    public $baseLocalUnitPriceObject;

    public $localStandardUnitPrice;

    public $createdByName;

    public $lastChangeByName;

    public $glAccountName;

    public $glAccountNumber;

    public $glAccountType;

    public $costCenterName;

    public $discountAmount;

    public $convertedDocQuantity;

    public $convertedDocUnitPrice;

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

    public $docDate;

    public $docYear;

    public $docMonth;

    public $docRevisionNo;

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

    public $itemModel;

    public $itemSerial;

    public $itemDefaultManufacturer;

    public $itemManufacturerModel;

    public $itemManufacturerSerial;

    public $itemManufacturerCode;

    public $itemKeywords;

    public $itemAssetLabel;

    public $itemAssetLabel1;

    public $itemDescription;

    public $itemInventoryGL;

    public $itemCogsGL;

    public $itemCostCenter;

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

    public $prDepartment;

    public $prDepartmentName;

    public $prWarehouse;

    public $prWarehouseName;

    /*
     * |=============================
     * | Procure\Domain\AbstractRow
     * |
     * |=============================
     */
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

    public $clearingDocId;

    public $brand;

    /**
     *
     * @return mixed
     */
    public function getExculdedProps()
    {
        return $this->exculdedProps;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomVO()
    {
        return $this->docUomVO;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUomVO()
    {
        return $this->itemStandardUomVO;
    }

    /**
     *
     * @return mixed
     */
    public function getUomPairVO()
    {
        return $this->uomPairVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantityVO()
    {
        return $this->docQuantityVO;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardQuantityVO()
    {
        return $this->itemStandardQuantityVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyVO()
    {
        return $this->docCurrencyVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyVO()
    {
        return $this->localCurrencyVO;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrencyPair()
    {
        return $this->currencyPair;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPriceVO()
    {
        return $this->docUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocItemStandardUnitPriceVO()
    {
        return $this->docItemStandardUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocNetAmountVO()
    {
        return $this->docNetAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocTaxAmountVO()
    {
        return $this->docTaxAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocGrossAmountVO()
    {
        return $this->docGrossAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPriceVO()
    {
        return $this->localUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalItemStandardUnitPriceVO()
    {
        return $this->localItemStandardUnitPriceVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmountVO()
    {
        return $this->LocalNetAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalTaxAmountVO()
    {
        return $this->localTaxAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalGrossAmountVO()
    {
        return $this->localGrossAmountVO;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQuantity()
    {
        return $this->standardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitPriceInDocCurrency()
    {
        return $this->standardUnitPriceInDocCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitPriceInLocCurrency()
    {
        return $this->standardUnitPriceInLocCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantityObject()
    {
        return $this->docQuantityObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUomPair()
    {
        return $this->baseUomPair;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPriceObject()
    {
        return $this->docUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseDocUnitPriceObject()
    {
        return $this->baseDocUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPriceObject()
    {
        return $this->localUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseLocalUnitPriceObject()
    {
        return $this->baseLocalUnitPriceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalStandardUnitPrice()
    {
        return $this->localStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeByName()
    {
        return $this->lastChangeByName;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountName()
    {
        return $this->glAccountName;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountNumber()
    {
        return $this->glAccountNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccountType()
    {
        return $this->glAccountType;
    }

    /**
     *
     * @return mixed
     */
    public function getCostCenterName()
    {
        return $this->costCenterName;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedDocQuantity()
    {
        return $this->convertedDocQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedDocUnitPrice()
    {
        return $this->convertedDocUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     *
     * @return mixed
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocSysNumber()
    {
        return $this->docSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocToken()
    {
        return $this->docToken;
    }

    /**
     *
     * @return mixed
     */
    public function getDocId()
    {
        return $this->docId;
    }

    /**
     *
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocDate()
    {
        return $this->docDate;
    }

    /**
     *
     * @return mixed
     */
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     *
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
    }

    /**
     *
     * @return mixed
     */
    public function getDocRevisionNo()
    {
        return $this->docRevisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getDocWarehouseName()
    {
        return $this->docWarehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getDocWarehouseCode()
    {
        return $this->docWarehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomName()
    {
        return $this->docUomName;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomCode()
    {
        return $this->docUomCode;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUomDescription()
    {
        return $this->docUomDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     *
     * @return mixed
     */
    public function getItemChecksum()
    {
        return $this->itemChecksum;
    }

    /**
     *
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemName1()
    {
        return $this->itemName1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnitCode()
    {
        return $this->itemStandardUnitCode;
    }

    /**
     *
     * @return mixed
     */
    public function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getIsInventoryItem()
    {
        return $this->isInventoryItem;
    }

    /**
     *
     * @return mixed
     */
    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     *
     * @return mixed
     */
    public function getItemMonitorMethod()
    {
        return $this->itemMonitorMethod;
    }

    /**
     *
     * @return mixed
     */
    public function getItemModel()
    {
        return $this->itemModel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSerial()
    {
        return $this->itemSerial;
    }

    /**
     *
     * @return mixed
     */
    public function getItemDefaultManufacturer()
    {
        return $this->itemDefaultManufacturer;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerModel()
    {
        return $this->itemManufacturerModel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerSerial()
    {
        return $this->itemManufacturerSerial;
    }

    /**
     *
     * @return mixed
     */
    public function getItemManufacturerCode()
    {
        return $this->itemManufacturerCode;
    }

    /**
     *
     * @return mixed
     */
    public function getItemKeywords()
    {
        return $this->itemKeywords;
    }

    /**
     *
     * @return mixed
     */
    public function getItemAssetLabel()
    {
        return $this->itemAssetLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getItemAssetLabel1()
    {
        return $this->itemAssetLabel1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getItemInventoryGL()
    {
        return $this->itemInventoryGL;
    }

    /**
     *
     * @return mixed
     */
    public function getItemCogsGL()
    {
        return $this->itemCogsGL;
    }

    /**
     *
     * @return mixed
     */
    public function getItemCostCenter()
    {
        return $this->itemCostCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     *
     * @return mixed
     */
    public function getPrToken()
    {
        return $this->prToken;
    }

    /**
     *
     * @return mixed
     */
    public function getPrChecksum()
    {
        return $this->prChecksum;
    }

    /**
     *
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowVersion()
    {
        return $this->prRowVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectToken()
    {
        return $this->projectToken;
    }

    /**
     *
     * @return mixed
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDepartment()
    {
        return $this->prDepartment;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDepartmentName()
    {
        return $this->prDepartmentName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrWarehouse()
    {
        return $this->prWarehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getPrWarehouseName()
    {
        return $this->prWarehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     *
     * @return mixed
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     *
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     *
     * @return mixed
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     *
     * @return mixed
     */
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     *
     * @return mixed
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardConvertFactor()
    {
        return $this->standardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getClearingDocId()
    {
        return $this->clearingDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     *
     * @param mixed $exculdedProps
     */
    public function setExculdedProps($exculdedProps)
    {
        $this->exculdedProps = $exculdedProps;
    }

    /**
     *
     * @param mixed $docUomVO
     */
    public function setDocUomVO($docUomVO)
    {
        $this->docUomVO = $docUomVO;
    }

    /**
     *
     * @param mixed $itemStandardUomVO
     */
    public function setItemStandardUomVO($itemStandardUomVO)
    {
        $this->itemStandardUomVO = $itemStandardUomVO;
    }

    /**
     *
     * @param mixed $uomPairVO
     */
    public function setUomPairVO($uomPairVO)
    {
        $this->uomPairVO = $uomPairVO;
    }

    /**
     *
     * @param mixed $docQuantityVO
     */
    public function setDocQuantityVO($docQuantityVO)
    {
        $this->docQuantityVO = $docQuantityVO;
    }

    /**
     *
     * @param mixed $itemStandardQuantityVO
     */
    public function setItemStandardQuantityVO($itemStandardQuantityVO)
    {
        $this->itemStandardQuantityVO = $itemStandardQuantityVO;
    }

    /**
     *
     * @param mixed $docCurrencyVO
     */
    public function setDocCurrencyVO($docCurrencyVO)
    {
        $this->docCurrencyVO = $docCurrencyVO;
    }

    /**
     *
     * @param mixed $localCurrencyVO
     */
    public function setLocalCurrencyVO($localCurrencyVO)
    {
        $this->localCurrencyVO = $localCurrencyVO;
    }

    /**
     *
     * @param mixed $currencyPair
     */
    public function setCurrencyPair($currencyPair)
    {
        $this->currencyPair = $currencyPair;
    }

    /**
     *
     * @param mixed $docUnitPriceVO
     */
    public function setDocUnitPriceVO($docUnitPriceVO)
    {
        $this->docUnitPriceVO = $docUnitPriceVO;
    }

    /**
     *
     * @param mixed $docItemStandardUnitPriceVO
     */
    public function setDocItemStandardUnitPriceVO($docItemStandardUnitPriceVO)
    {
        $this->docItemStandardUnitPriceVO = $docItemStandardUnitPriceVO;
    }

    /**
     *
     * @param mixed $docNetAmountVO
     */
    public function setDocNetAmountVO($docNetAmountVO)
    {
        $this->docNetAmountVO = $docNetAmountVO;
    }

    /**
     *
     * @param mixed $docTaxAmountVO
     */
    public function setDocTaxAmountVO($docTaxAmountVO)
    {
        $this->docTaxAmountVO = $docTaxAmountVO;
    }

    /**
     *
     * @param mixed $docGrossAmountVO
     */
    public function setDocGrossAmountVO($docGrossAmountVO)
    {
        $this->docGrossAmountVO = $docGrossAmountVO;
    }

    /**
     *
     * @param mixed $localUnitPriceVO
     */
    public function setLocalUnitPriceVO($localUnitPriceVO)
    {
        $this->localUnitPriceVO = $localUnitPriceVO;
    }

    /**
     *
     * @param mixed $localItemStandardUnitPriceVO
     */
    public function setLocalItemStandardUnitPriceVO($localItemStandardUnitPriceVO)
    {
        $this->localItemStandardUnitPriceVO = $localItemStandardUnitPriceVO;
    }

    /**
     *
     * @param mixed $LocalNetAmountVO
     */
    public function setLocalNetAmountVO($LocalNetAmountVO)
    {
        $this->LocalNetAmountVO = $LocalNetAmountVO;
    }

    /**
     *
     * @param mixed $localTaxAmountVO
     */
    public function setLocalTaxAmountVO($localTaxAmountVO)
    {
        $this->localTaxAmountVO = $localTaxAmountVO;
    }

    /**
     *
     * @param mixed $localGrossAmountVO
     */
    public function setLocalGrossAmountVO($localGrossAmountVO)
    {
        $this->localGrossAmountVO = $localGrossAmountVO;
    }

    /**
     *
     * @param mixed $standardQuantity
     */
    public function setStandardQuantity($standardQuantity)
    {
        $this->standardQuantity = $standardQuantity;
    }

    /**
     *
     * @param mixed $standardUnitPriceInDocCurrency
     */
    public function setStandardUnitPriceInDocCurrency($standardUnitPriceInDocCurrency)
    {
        $this->standardUnitPriceInDocCurrency = $standardUnitPriceInDocCurrency;
    }

    /**
     *
     * @param mixed $standardUnitPriceInLocCurrency
     */
    public function setStandardUnitPriceInLocCurrency($standardUnitPriceInLocCurrency)
    {
        $this->standardUnitPriceInLocCurrency = $standardUnitPriceInLocCurrency;
    }

    /**
     *
     * @param mixed $docQuantityObject
     */
    public function setDocQuantityObject($docQuantityObject)
    {
        $this->docQuantityObject = $docQuantityObject;
    }

    /**
     *
     * @param mixed $baseUomPair
     */
    public function setBaseUomPair($baseUomPair)
    {
        $this->baseUomPair = $baseUomPair;
    }

    /**
     *
     * @param mixed $docUnitPriceObject
     */
    public function setDocUnitPriceObject($docUnitPriceObject)
    {
        $this->docUnitPriceObject = $docUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseDocUnitPriceObject
     */
    public function setBaseDocUnitPriceObject($baseDocUnitPriceObject)
    {
        $this->baseDocUnitPriceObject = $baseDocUnitPriceObject;
    }

    /**
     *
     * @param mixed $localUnitPriceObject
     */
    public function setLocalUnitPriceObject($localUnitPriceObject)
    {
        $this->localUnitPriceObject = $localUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseLocalUnitPriceObject
     */
    public function setBaseLocalUnitPriceObject($baseLocalUnitPriceObject)
    {
        $this->baseLocalUnitPriceObject = $baseLocalUnitPriceObject;
    }

    /**
     *
     * @param mixed $localStandardUnitPrice
     */
    public function setLocalStandardUnitPrice($localStandardUnitPrice)
    {
        $this->localStandardUnitPrice = $localStandardUnitPrice;
    }

    /**
     *
     * @param mixed $createdByName
     */
    public function setCreatedByName($createdByName)
    {
        $this->createdByName = $createdByName;
    }

    /**
     *
     * @param mixed $lastChangeByName
     */
    public function setLastChangeByName($lastChangeByName)
    {
        $this->lastChangeByName = $lastChangeByName;
    }

    /**
     *
     * @param mixed $glAccountName
     */
    public function setGlAccountName($glAccountName)
    {
        $this->glAccountName = $glAccountName;
    }

    /**
     *
     * @param mixed $glAccountNumber
     */
    public function setGlAccountNumber($glAccountNumber)
    {
        $this->glAccountNumber = $glAccountNumber;
    }

    /**
     *
     * @param mixed $glAccountType
     */
    public function setGlAccountType($glAccountType)
    {
        $this->glAccountType = $glAccountType;
    }

    /**
     *
     * @param mixed $costCenterName
     */
    public function setCostCenterName($costCenterName)
    {
        $this->costCenterName = $costCenterName;
    }

    /**
     *
     * @param mixed $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     *
     * @param mixed $convertedDocQuantity
     */
    public function setConvertedDocQuantity($convertedDocQuantity)
    {
        $this->convertedDocQuantity = $convertedDocQuantity;
    }

    /**
     *
     * @param mixed $convertedDocUnitPrice
     */
    public function setConvertedDocUnitPrice($convertedDocUnitPrice)
    {
        $this->convertedDocUnitPrice = $convertedDocUnitPrice;
    }

    /**
     *
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @param mixed $companyToken
     */
    public function setCompanyToken($companyToken)
    {
        $this->companyToken = $companyToken;
    }

    /**
     *
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * @param mixed $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     *
     * @param mixed $vendorToken
     */
    public function setVendorToken($vendorToken)
    {
        $this->vendorToken = $vendorToken;
    }

    /**
     *
     * @param mixed $vendorName
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     *
     * @param mixed $vendorCountry
     */
    public function setVendorCountry($vendorCountry)
    {
        $this->vendorCountry = $vendorCountry;
    }

    /**
     *
     * @param mixed $docNumber
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;
    }

    /**
     *
     * @param mixed $docSysNumber
     */
    public function setDocSysNumber($docSysNumber)
    {
        $this->docSysNumber = $docSysNumber;
    }

    /**
     *
     * @param mixed $docCurrencyISO
     */
    public function setDocCurrencyISO($docCurrencyISO)
    {
        $this->docCurrencyISO = $docCurrencyISO;
    }

    /**
     *
     * @param mixed $localCurrencyISO
     */
    public function setLocalCurrencyISO($localCurrencyISO)
    {
        $this->localCurrencyISO = $localCurrencyISO;
    }

    /**
     *
     * @param mixed $docCurrencyId
     */
    public function setDocCurrencyId($docCurrencyId)
    {
        $this->docCurrencyId = $docCurrencyId;
    }

    /**
     *
     * @param mixed $localCurrencyId
     */
    public function setLocalCurrencyId($localCurrencyId)
    {
        $this->localCurrencyId = $localCurrencyId;
    }

    /**
     *
     * @param mixed $docToken
     */
    public function setDocToken($docToken)
    {
        $this->docToken = $docToken;
    }

    /**
     *
     * @param mixed $docId
     */
    public function setDocId($docId)
    {
        $this->docId = $docId;
    }

    /**
     *
     * @param mixed $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     *
     * @param mixed $docDate
     */
    public function setDocDate($docDate)
    {
        $this->docDate = $docDate;
    }

    /**
     *
     * @param mixed $docYear
     */
    public function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @param mixed $docMonth
     */
    public function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
    }

    /**
     *
     * @param mixed $docRevisionNo
     */
    public function setDocRevisionNo($docRevisionNo)
    {
        $this->docRevisionNo = $docRevisionNo;
    }

    /**
     *
     * @param mixed $docWarehouseName
     */
    public function setDocWarehouseName($docWarehouseName)
    {
        $this->docWarehouseName = $docWarehouseName;
    }

    /**
     *
     * @param mixed $docWarehouseCode
     */
    public function setDocWarehouseCode($docWarehouseCode)
    {
        $this->docWarehouseCode = $docWarehouseCode;
    }

    /**
     *
     * @param mixed $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     *
     * @param mixed $warehouseCode
     */
    public function setWarehouseCode($warehouseCode)
    {
        $this->warehouseCode = $warehouseCode;
    }

    /**
     *
     * @param mixed $docUomName
     */
    public function setDocUomName($docUomName)
    {
        $this->docUomName = $docUomName;
    }

    /**
     *
     * @param mixed $docUomCode
     */
    public function setDocUomCode($docUomCode)
    {
        $this->docUomCode = $docUomCode;
    }

    /**
     *
     * @param mixed $docUomDescription
     */
    public function setDocUomDescription($docUomDescription)
    {
        $this->docUomDescription = $docUomDescription;
    }

    /**
     *
     * @param mixed $itemToken
     */
    public function setItemToken($itemToken)
    {
        $this->itemToken = $itemToken;
    }

    /**
     *
     * @param mixed $itemChecksum
     */
    public function setItemChecksum($itemChecksum)
    {
        $this->itemChecksum = $itemChecksum;
    }

    /**
     *
     * @param mixed $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     *
     * @param mixed $itemName1
     */
    public function setItemName1($itemName1)
    {
        $this->itemName1 = $itemName1;
    }

    /**
     *
     * @param mixed $itemSKU
     */
    public function setItemSKU($itemSKU)
    {
        $this->itemSKU = $itemSKU;
    }

    /**
     *
     * @param mixed $itemSKU1
     */
    public function setItemSKU1($itemSKU1)
    {
        $this->itemSKU1 = $itemSKU1;
    }

    /**
     *
     * @param mixed $itemSKU2
     */
    public function setItemSKU2($itemSKU2)
    {
        $this->itemSKU2 = $itemSKU2;
    }

    /**
     *
     * @param mixed $itemUUID
     */
    public function setItemUUID($itemUUID)
    {
        $this->itemUUID = $itemUUID;
    }

    /**
     *
     * @param mixed $itemSysNumber
     */
    public function setItemSysNumber($itemSysNumber)
    {
        $this->itemSysNumber = $itemSysNumber;
    }

    /**
     *
     * @param mixed $itemStandardUnit
     */
    public function setItemStandardUnit($itemStandardUnit)
    {
        $this->itemStandardUnit = $itemStandardUnit;
    }

    /**
     *
     * @param mixed $itemStandardUnitName
     */
    public function setItemStandardUnitName($itemStandardUnitName)
    {
        $this->itemStandardUnitName = $itemStandardUnitName;
    }

    /**
     *
     * @param mixed $itemStandardUnitCode
     */
    public function setItemStandardUnitCode($itemStandardUnitCode)
    {
        $this->itemStandardUnitCode = $itemStandardUnitCode;
    }

    /**
     *
     * @param mixed $itemVersion
     */
    public function setItemVersion($itemVersion)
    {
        $this->itemVersion = $itemVersion;
    }

    /**
     *
     * @param mixed $isInventoryItem
     */
    public function setIsInventoryItem($isInventoryItem)
    {
        $this->isInventoryItem = $isInventoryItem;
    }

    /**
     *
     * @param mixed $isFixedAsset
     */
    public function setIsFixedAsset($isFixedAsset)
    {
        $this->isFixedAsset = $isFixedAsset;
    }

    /**
     *
     * @param mixed $itemMonitorMethod
     */
    public function setItemMonitorMethod($itemMonitorMethod)
    {
        $this->itemMonitorMethod = $itemMonitorMethod;
    }

    /**
     *
     * @param mixed $itemModel
     */
    public function setItemModel($itemModel)
    {
        $this->itemModel = $itemModel;
    }

    /**
     *
     * @param mixed $itemSerial
     */
    public function setItemSerial($itemSerial)
    {
        $this->itemSerial = $itemSerial;
    }

    /**
     *
     * @param mixed $itemDefaultManufacturer
     */
    public function setItemDefaultManufacturer($itemDefaultManufacturer)
    {
        $this->itemDefaultManufacturer = $itemDefaultManufacturer;
    }

    /**
     *
     * @param mixed $itemManufacturerModel
     */
    public function setItemManufacturerModel($itemManufacturerModel)
    {
        $this->itemManufacturerModel = $itemManufacturerModel;
    }

    /**
     *
     * @param mixed $itemManufacturerSerial
     */
    public function setItemManufacturerSerial($itemManufacturerSerial)
    {
        $this->itemManufacturerSerial = $itemManufacturerSerial;
    }

    /**
     *
     * @param mixed $itemManufacturerCode
     */
    public function setItemManufacturerCode($itemManufacturerCode)
    {
        $this->itemManufacturerCode = $itemManufacturerCode;
    }

    /**
     *
     * @param mixed $itemKeywords
     */
    public function setItemKeywords($itemKeywords)
    {
        $this->itemKeywords = $itemKeywords;
    }

    /**
     *
     * @param mixed $itemAssetLabel
     */
    public function setItemAssetLabel($itemAssetLabel)
    {
        $this->itemAssetLabel = $itemAssetLabel;
    }

    /**
     *
     * @param mixed $itemAssetLabel1
     */
    public function setItemAssetLabel1($itemAssetLabel1)
    {
        $this->itemAssetLabel1 = $itemAssetLabel1;
    }

    /**
     *
     * @param mixed $itemDescription
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     *
     * @param mixed $itemInventoryGL
     */
    public function setItemInventoryGL($itemInventoryGL)
    {
        $this->itemInventoryGL = $itemInventoryGL;
    }

    /**
     *
     * @param mixed $itemCogsGL
     */
    public function setItemCogsGL($itemCogsGL)
    {
        $this->itemCogsGL = $itemCogsGL;
    }

    /**
     *
     * @param mixed $itemCostCenter
     */
    public function setItemCostCenter($itemCostCenter)
    {
        $this->itemCostCenter = $itemCostCenter;
    }

    /**
     *
     * @param mixed $pr
     */
    public function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     *
     * @param mixed $prToken
     */
    public function setPrToken($prToken)
    {
        $this->prToken = $prToken;
    }

    /**
     *
     * @param mixed $prChecksum
     */
    public function setPrChecksum($prChecksum)
    {
        $this->prChecksum = $prChecksum;
    }

    /**
     *
     * @param mixed $prNumber
     */
    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;
    }

    /**
     *
     * @param mixed $prSysNumber
     */
    public function setPrSysNumber($prSysNumber)
    {
        $this->prSysNumber = $prSysNumber;
    }

    /**
     *
     * @param mixed $prRowIndentifer
     */
    public function setPrRowIndentifer($prRowIndentifer)
    {
        $this->prRowIndentifer = $prRowIndentifer;
    }

    /**
     *
     * @param mixed $prRowCode
     */
    public function setPrRowCode($prRowCode)
    {
        $this->prRowCode = $prRowCode;
    }

    /**
     *
     * @param mixed $prRowName
     */
    public function setPrRowName($prRowName)
    {
        $this->prRowName = $prRowName;
    }

    /**
     *
     * @param mixed $prRowConvertFactor
     */
    public function setPrRowConvertFactor($prRowConvertFactor)
    {
        $this->prRowConvertFactor = $prRowConvertFactor;
    }

    /**
     *
     * @param mixed $prRowUnit
     */
    public function setPrRowUnit($prRowUnit)
    {
        $this->prRowUnit = $prRowUnit;
    }

    /**
     *
     * @param mixed $prRowVersion
     */
    public function setPrRowVersion($prRowVersion)
    {
        $this->prRowVersion = $prRowVersion;
    }

    /**
     *
     * @param mixed $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     *
     * @param mixed $projectToken
     */
    public function setProjectToken($projectToken)
    {
        $this->projectToken = $projectToken;
    }

    /**
     *
     * @param mixed $projectName
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     *
     * @param mixed $prDepartment
     */
    public function setPrDepartment($prDepartment)
    {
        $this->prDepartment = $prDepartment;
    }

    /**
     *
     * @param mixed $prDepartmentName
     */
    public function setPrDepartmentName($prDepartmentName)
    {
        $this->prDepartmentName = $prDepartmentName;
    }

    /**
     *
     * @param mixed $prWarehouse
     */
    public function setPrWarehouse($prWarehouse)
    {
        $this->prWarehouse = $prWarehouse;
    }

    /**
     *
     * @param mixed $prWarehouseName
     */
    public function setPrWarehouseName($prWarehouseName)
    {
        $this->prWarehouseName = $prWarehouseName;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $rowNumber
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     *
     * @param mixed $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     *
     * @param mixed $netAmount
     */
    public function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @param mixed $itemUnit
     */
    public function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;
    }

    /**
     *
     * @param mixed $conversionFactor
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     *
     * @param mixed $converstionText
     */
    public function setConverstionText($converstionText)
    {
        $this->converstionText = $converstionText;
    }

    /**
     *
     * @param mixed $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @param mixed $currentState
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     *
     * @param mixed $vendorItemCode
     */
    public function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;
    }

    /**
     *
     * @param mixed $traceStock
     */
    public function setTraceStock($traceStock)
    {
        $this->traceStock = $traceStock;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    public function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @param mixed $faRemarks
     */
    public function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @param mixed $discountRate
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $targetObject
     */
    public function setTargetObject($targetObject)
    {
        $this->targetObject = $targetObject;
    }

    /**
     *
     * @param mixed $sourceObject
     */
    public function setSourceObject($sourceObject)
    {
        $this->sourceObject = $sourceObject;
    }

    /**
     *
     * @param mixed $targetObjectId
     */
    public function setTargetObjectId($targetObjectId)
    {
        $this->targetObjectId = $targetObjectId;
    }

    /**
     *
     * @param mixed $sourceObjectId
     */
    public function setSourceObjectId($sourceObjectId)
    {
        $this->sourceObjectId = $sourceObjectId;
    }

    /**
     *
     * @param mixed $docStatus
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     *
     * @param mixed $workflowStatus
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     *
     * @param mixed $transactionStatus
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     *
     * @param mixed $isPosted
     */
    public function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     *
     * @param mixed $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     *
     * @param mixed $exwUnitPrice
     */
    public function setExwUnitPrice($exwUnitPrice)
    {
        $this->exwUnitPrice = $exwUnitPrice;
    }

    /**
     *
     * @param mixed $totalExwPrice
     */
    public function setTotalExwPrice($totalExwPrice)
    {
        $this->totalExwPrice = $totalExwPrice;
    }

    /**
     *
     * @param mixed $convertFactorPurchase
     */
    public function setConvertFactorPurchase($convertFactorPurchase)
    {
        $this->convertFactorPurchase = $convertFactorPurchase;
    }

    /**
     *
     * @param mixed $convertedPurchaseQuantity
     */
    public function setConvertedPurchaseQuantity($convertedPurchaseQuantity)
    {
        $this->convertedPurchaseQuantity = $convertedPurchaseQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardQuantity
     */
    public function setConvertedStandardQuantity($convertedStandardQuantity)
    {
        $this->convertedStandardQuantity = $convertedStandardQuantity;
    }

    /**
     *
     * @param mixed $convertedStockQuantity
     */
    public function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardUnitPrice
     */
    public function setConvertedStandardUnitPrice($convertedStandardUnitPrice)
    {
        $this->convertedStandardUnitPrice = $convertedStandardUnitPrice;
    }

    /**
     *
     * @param mixed $convertedStockUnitPrice
     */
    public function setConvertedStockUnitPrice($convertedStockUnitPrice)
    {
        $this->convertedStockUnitPrice = $convertedStockUnitPrice;
    }

    /**
     *
     * @param mixed $docQuantity
     */
    public function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;
    }

    /**
     *
     * @param mixed $docUnit
     */
    public function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;
    }

    /**
     *
     * @param mixed $docUnitPrice
     */
    public function setDocUnitPrice($docUnitPrice)
    {
        $this->docUnitPrice = $docUnitPrice;
    }

    /**
     *
     * @param mixed $convertedPurchaseUnitPrice
     */
    public function setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice)
    {
        $this->convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice;
    }

    /**
     *
     * @param mixed $docType
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @param mixed $descriptionText
     */
    public function setDescriptionText($descriptionText)
    {
        $this->descriptionText = $descriptionText;
    }

    /**
     *
     * @param mixed $vendorItemName
     */
    public function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;
    }

    /**
     *
     * @param mixed $reversalBlocked
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     *
     * @param mixed $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    public function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @param mixed $prRow
     */
    public function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @param mixed $po
     */
    public function setPo($po)
    {
        $this->po = $po;
    }

    /**
     *
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @param mixed $docUom
     */
    public function setDocUom($docUom)
    {
        $this->docUom = $docUom;
    }

    /**
     *
     * @param mixed $docVersion
     */
    public function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $localUnitPrice
     */
    public function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;
    }

    /**
     *
     * @param mixed $exwCurrency
     */
    public function setExwCurrency($exwCurrency)
    {
        $this->exwCurrency = $exwCurrency;
    }

    /**
     *
     * @param mixed $localNetAmount
     */
    public function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;
    }

    /**
     *
     * @param mixed $localGrossAmount
     */
    public function setLocalGrossAmount($localGrossAmount)
    {
        $this->localGrossAmount = $localGrossAmount;
    }

    /**
     *
     * @param mixed $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     *
     * @param mixed $isReversed
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @param mixed $glAccount
     */
    public function setGlAccount($glAccount)
    {
        $this->glAccount = $glAccount;
    }

    /**
     *
     * @param mixed $costCenter
     */
    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    /**
     *
     * @param mixed $standardConvertFactor
     */
    public function setStandardConvertFactor($standardConvertFactor)
    {
        $this->standardConvertFactor = $standardConvertFactor;
    }

    /**
     *
     * @param mixed $clearingDocId
     */
    public function setClearingDocId($clearingDocId)
    {
        $this->clearingDocId = $clearingDocId;
    }

    /**
     *
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }
}


