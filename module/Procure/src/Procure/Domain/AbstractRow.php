<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractEntity;

/**
 * Abstract Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractRow extends AbstractEntity
{

    // Doc Details
    // ====================
    protected $companyId;

    protected $companyToken;

    protected $companyName;

    protected $vendorId;

    protected $vendorToken;

    protected $vendorName;

    protected $vendorCountry;

    protected $docNumber;

    protected $docSysNumber;

    protected $docCurrencyISO;

    protected $localCurrencyISO;

    protected $docCurrencyId;

    protected $localCurrencyId;

    protected $docToken;

    protected $docId;

    protected $exchangeRate;

    protected $docDate;

    protected $docYear;

    protected $docMonth;

    // Warehouse Details
    // ====================
    protected $docWarehouseName;

    protected $docWarehouseCode;

    protected $warehouseName;

    protected $warehouseCode;

    // UOM Details
    // ====================
    protected $docUomName;

    protected $docUomCode;

    protected $docUomDescription;

    // Item Details
    // ====================
    protected $itemToken;

    protected $itemChecksum;

    protected $itemName;

    protected $itemName1;

    protected $itemSKU;

    protected $itemSKU1;

    protected $itemSKU2;

    protected $itemUUID;

    protected $itemSysNumber;

    protected $itemStandardUnit;

    protected $itemStandardUnitName;

    protected $itemStandardUnitCode;

    protected $itemVersion;

    protected $isInventoryItem;

    protected $isFixedAsset;

    protected $itemMonitorMethod;

    protected $itemModel;

    protected $itemSerial;

    protected $itemDefaultManufacturer;

    protected $itemManufacturerModel;

    protected $itemManufacturerSerial;

    protected $itemManufacturerCode;

    protected $itemKeywords;

    protected $itemAssetLabel;

    protected $itemAssetLabel1;

    // Item Group Detail
    // ========================
    protected $itemInventoryGL;

    protected $itemCogsGL;

    protected $itemCostCenter;

    // PR Details
    // ====================
    protected $pr;

    protected $prToken;

    protected $prChecksum;

    protected $prNumber;

    protected $prSysNumber;

    protected $prRowIndentifer;

    protected $prRowCode;

    protected $prRowName;

    protected $prRowConvertFactor;

    protected $prRowUnit;

    protected $prRowVersion;

    protected $projectId;

    protected $projectToken;

    protected $projectName;

    // Row Details
    // ====================
    protected $createdByName;

    protected $lastChangeByName;

    protected $glAccountName;

    protected $glAccountNumber;

    protected $glAccountType;

    protected $costCenterName;

    protected $discountAmount;

    // Row Original
    // ====================
    protected $id;

    protected $rowNumber;

    protected $token;

    protected $quantity;

    protected $unitPrice;

    protected $netAmount;

    protected $unit;

    protected $itemUnit;

    protected $conversionFactor;

    protected $converstionText;

    protected $taxRate;

    protected $remarks;

    protected $isActive;

    protected $createdOn;

    protected $lastchangeOn;

    protected $currentState;

    protected $vendorItemCode;

    protected $traceStock;

    protected $grossAmount;

    protected $taxAmount;

    protected $faRemarks;

    protected $rowIdentifer;

    protected $discountRate;

    protected $revisionNo;

    protected $targetObject;

    protected $sourceObject;

    protected $targetObjectId;

    protected $sourceObjectId;

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $isPosted;

    protected $isDraft;

    protected $exwUnitPrice;

    protected $totalExwPrice;

    protected $convertFactorPurchase;

    protected $convertedPurchaseQuantity;

    protected $convertedStandardQuantity;

    protected $convertedStockQuantity;

    protected $convertedStandardUnitPrice;

    protected $convertedStockUnitPrice;

    protected $docQuantity;

    protected $docUnit;

    protected $docUnitPrice;

    protected $convertedPurchaseUnitPrice;

    protected $docType;

    protected $descriptionText;

    protected $vendorItemName;

    protected $reversalBlocked;

    protected $invoice;

    protected $lastchangeBy;

    protected $prRow;

    protected $createdBy;

    protected $warehouse;

    protected $po;

    protected $item;

    protected $docUom;

    protected $docVersion;

    protected $uuid;

    protected $localUnitPrice;

    protected $exwCurrency;

    protected $localNetAmount;

    protected $localGrossAmount;

    protected $transactionType;

    protected $isReversed;

    protected $reversalDate;

    protected $glAccount;

    protected $costCenter;

    protected $standardConvertFactor;

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
     * @param mixed $docDate
     */
    protected function setDocDate($docDate)
    {
        $this->docDate = $docDate;
    }

    /**
     *
     * @param mixed $docYear
     */
    protected function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @param mixed $docMonth
     */
    protected function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
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
     * @param mixed $discountAmount
     */
    protected function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
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
     * @param mixed $companyId
     */
    protected function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @param mixed $companyToken
     */
    protected function setCompanyToken($companyToken)
    {
        $this->companyToken = $companyToken;
    }

    /**
     *
     * @param mixed $companyName
     */
    protected function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * @param mixed $vendorId
     */
    protected function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     *
     * @param mixed $vendorToken
     */
    protected function setVendorToken($vendorToken)
    {
        $this->vendorToken = $vendorToken;
    }

    /**
     *
     * @param mixed $vendorName
     */
    protected function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     *
     * @param mixed $vendorCountry
     */
    protected function setVendorCountry($vendorCountry)
    {
        $this->vendorCountry = $vendorCountry;
    }

    /**
     *
     * @param mixed $docNumber
     */
    protected function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;
    }

    /**
     *
     * @param mixed $docSysNumber
     */
    protected function setDocSysNumber($docSysNumber)
    {
        $this->docSysNumber = $docSysNumber;
    }

    /**
     *
     * @param mixed $docCurrencyISO
     */
    protected function setDocCurrencyISO($docCurrencyISO)
    {
        $this->docCurrencyISO = $docCurrencyISO;
    }

    /**
     *
     * @param mixed $localCurrencyISO
     */
    protected function setLocalCurrencyISO($localCurrencyISO)
    {
        $this->localCurrencyISO = $localCurrencyISO;
    }

    /**
     *
     * @param mixed $docCurrencyId
     */
    protected function setDocCurrencyId($docCurrencyId)
    {
        $this->docCurrencyId = $docCurrencyId;
    }

    /**
     *
     * @param mixed $localCurrencyId
     */
    protected function setLocalCurrencyId($localCurrencyId)
    {
        $this->localCurrencyId = $localCurrencyId;
    }

    /**
     *
     * @param mixed $docToken
     */
    protected function setDocToken($docToken)
    {
        $this->docToken = $docToken;
    }

    /**
     *
     * @param mixed $docId
     */
    protected function setDocId($docId)
    {
        $this->docId = $docId;
    }

    /**
     *
     * @param mixed $exchangeRate
     */
    protected function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     *
     * @param mixed $docWarehouseName
     */
    protected function setDocWarehouseName($docWarehouseName)
    {
        $this->docWarehouseName = $docWarehouseName;
    }

    /**
     *
     * @param mixed $docWarehouseCode
     */
    protected function setDocWarehouseCode($docWarehouseCode)
    {
        $this->docWarehouseCode = $docWarehouseCode;
    }

    /**
     *
     * @param mixed $warehouseName
     */
    protected function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     *
     * @param mixed $warehouseCode
     */
    protected function setWarehouseCode($warehouseCode)
    {
        $this->warehouseCode = $warehouseCode;
    }

    /**
     *
     * @param mixed $docUomName
     */
    protected function setDocUomName($docUomName)
    {
        $this->docUomName = $docUomName;
    }

    /**
     *
     * @param mixed $docUomCode
     */
    protected function setDocUomCode($docUomCode)
    {
        $this->docUomCode = $docUomCode;
    }

    /**
     *
     * @param mixed $docUomDescription
     */
    protected function setDocUomDescription($docUomDescription)
    {
        $this->docUomDescription = $docUomDescription;
    }

    /**
     *
     * @param mixed $itemToken
     */
    protected function setItemToken($itemToken)
    {
        $this->itemToken = $itemToken;
    }

    /**
     *
     * @param mixed $itemChecksum
     */
    protected function setItemChecksum($itemChecksum)
    {
        $this->itemChecksum = $itemChecksum;
    }

    /**
     *
     * @param mixed $itemName
     */
    protected function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     *
     * @param mixed $itemName1
     */
    protected function setItemName1($itemName1)
    {
        $this->itemName1 = $itemName1;
    }

    /**
     *
     * @param mixed $itemSKU
     */
    protected function setItemSKU($itemSKU)
    {
        $this->itemSKU = $itemSKU;
    }

    /**
     *
     * @param mixed $itemSKU1
     */
    protected function setItemSKU1($itemSKU1)
    {
        $this->itemSKU1 = $itemSKU1;
    }

    /**
     *
     * @param mixed $itemSKU2
     */
    protected function setItemSKU2($itemSKU2)
    {
        $this->itemSKU2 = $itemSKU2;
    }

    /**
     *
     * @param mixed $itemUUID
     */
    protected function setItemUUID($itemUUID)
    {
        $this->itemUUID = $itemUUID;
    }

    /**
     *
     * @param mixed $itemSysNumber
     */
    protected function setItemSysNumber($itemSysNumber)
    {
        $this->itemSysNumber = $itemSysNumber;
    }

    /**
     *
     * @param mixed $itemStandardUnit
     */
    protected function setItemStandardUnit($itemStandardUnit)
    {
        $this->itemStandardUnit = $itemStandardUnit;
    }

    /**
     *
     * @param mixed $itemStandardUnitName
     */
    protected function setItemStandardUnitName($itemStandardUnitName)
    {
        $this->itemStandardUnitName = $itemStandardUnitName;
    }

    /**
     *
     * @param mixed $itemStandardUnitCode
     */
    protected function setItemStandardUnitCode($itemStandardUnitCode)
    {
        $this->itemStandardUnitCode = $itemStandardUnitCode;
    }

    /**
     *
     * @param mixed $itemVersion
     */
    protected function setItemVersion($itemVersion)
    {
        $this->itemVersion = $itemVersion;
    }

    /**
     *
     * @param mixed $isInventoryItem
     */
    protected function setIsInventoryItem($isInventoryItem)
    {
        $this->isInventoryItem = $isInventoryItem;
    }

    /**
     *
     * @param mixed $isFixedAsset
     */
    protected function setIsFixedAsset($isFixedAsset)
    {
        $this->isFixedAsset = $isFixedAsset;
    }

    /**
     *
     * @param mixed $itemMonitorMethod
     */
    protected function setItemMonitorMethod($itemMonitorMethod)
    {
        $this->itemMonitorMethod = $itemMonitorMethod;
    }

    /**
     *
     * @param mixed $pr
     */
    protected function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     *
     * @param mixed $prToken
     */
    protected function setPrToken($prToken)
    {
        $this->prToken = $prToken;
    }

    /**
     *
     * @param mixed $prChecksum
     */
    protected function setPrChecksum($prChecksum)
    {
        $this->prChecksum = $prChecksum;
    }

    /**
     *
     * @param mixed $prNumber
     */
    protected function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;
    }

    /**
     *
     * @param mixed $prSysNumber
     */
    protected function setPrSysNumber($prSysNumber)
    {
        $this->prSysNumber = $prSysNumber;
    }

    /**
     *
     * @param mixed $prRowIndentifer
     */
    protected function setPrRowIndentifer($prRowIndentifer)
    {
        $this->prRowIndentifer = $prRowIndentifer;
    }

    /**
     *
     * @param mixed $prRowCode
     */
    protected function setPrRowCode($prRowCode)
    {
        $this->prRowCode = $prRowCode;
    }

    /**
     *
     * @param mixed $prRowName
     */
    protected function setPrRowName($prRowName)
    {
        $this->prRowName = $prRowName;
    }

    /**
     *
     * @param mixed $prRowConvertFactor
     */
    protected function setPrRowConvertFactor($prRowConvertFactor)
    {
        $this->prRowConvertFactor = $prRowConvertFactor;
    }

    /**
     *
     * @param mixed $prRowUnit
     */
    protected function setPrRowUnit($prRowUnit)
    {
        $this->prRowUnit = $prRowUnit;
    }

    /**
     *
     * @param mixed $prRowVersion
     */
    protected function setPrRowVersion($prRowVersion)
    {
        $this->prRowVersion = $prRowVersion;
    }

    /**
     *
     * @param mixed $projectId
     */
    protected function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     *
     * @param mixed $projectToken
     */
    protected function setProjectToken($projectToken)
    {
        $this->projectToken = $projectToken;
    }

    /**
     *
     * @param mixed $projectName
     */
    protected function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     *
     * @param mixed $createdByName
     */
    protected function setCreatedByName($createdByName)
    {
        $this->createdByName = $createdByName;
    }

    /**
     *
     * @param mixed $lastChangeByName
     */
    protected function setLastChangeByName($lastChangeByName)
    {
        $this->lastChangeByName = $lastChangeByName;
    }

    /**
     *
     * @param mixed $glAccountName
     */
    protected function setGlAccountName($glAccountName)
    {
        $this->glAccountName = $glAccountName;
    }

    /**
     *
     * @param mixed $glAccountNumber
     */
    protected function setGlAccountNumber($glAccountNumber)
    {
        $this->glAccountNumber = $glAccountNumber;
    }

    /**
     *
     * @param mixed $glAccountType
     */
    protected function setGlAccountType($glAccountType)
    {
        $this->glAccountType = $glAccountType;
    }

    /**
     *
     * @param mixed $costCenterName
     */
    protected function setCostCenterName($costCenterName)
    {
        $this->costCenterName = $costCenterName;
    }

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $rowNumber
     */
    protected function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $quantity
     */
    protected function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     *
     * @param mixed $unitPrice
     */
    protected function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     *
     * @param mixed $netAmount
     */
    protected function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @param mixed $unit
     */
    protected function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @param mixed $itemUnit
     */
    protected function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;
    }

    /**
     *
     * @param mixed $conversionFactor
     */
    protected function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     *
     * @param mixed $converstionText
     */
    protected function setConverstionText($converstionText)
    {
        $this->converstionText = $converstionText;
    }

    /**
     *
     * @param mixed $taxRate
     */
    protected function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    protected function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @param mixed $currentState
     */
    protected function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     *
     * @param mixed $vendorItemCode
     */
    protected function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;
    }

    /**
     *
     * @param mixed $traceStock
     */
    protected function setTraceStock($traceStock)
    {
        $this->traceStock = $traceStock;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    protected function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    protected function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @param mixed $faRemarks
     */
    protected function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    protected function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @param mixed $discountRate
     */
    protected function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $targetObject
     */
    protected function setTargetObject($targetObject)
    {
        $this->targetObject = $targetObject;
    }

    /**
     *
     * @param mixed $sourceObject
     */
    protected function setSourceObject($sourceObject)
    {
        $this->sourceObject = $sourceObject;
    }

    /**
     *
     * @param mixed $targetObjectId
     */
    protected function setTargetObjectId($targetObjectId)
    {
        $this->targetObjectId = $targetObjectId;
    }

    /**
     *
     * @param mixed $sourceObjectId
     */
    protected function setSourceObjectId($sourceObjectId)
    {
        $this->sourceObjectId = $sourceObjectId;
    }

    /**
     *
     * @param mixed $docStatus
     */
    protected function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     *
     * @param mixed $workflowStatus
     */
    protected function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     *
     * @param mixed $transactionStatus
     */
    protected function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     *
     * @param mixed $isPosted
     */
    protected function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     *
     * @param mixed $isDraft
     */
    protected function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     *
     * @param mixed $exwUnitPrice
     */
    protected function setExwUnitPrice($exwUnitPrice)
    {
        $this->exwUnitPrice = $exwUnitPrice;
    }

    /**
     *
     * @param mixed $totalExwPrice
     */
    protected function setTotalExwPrice($totalExwPrice)
    {
        $this->totalExwPrice = $totalExwPrice;
    }

    /**
     *
     * @param mixed $convertFactorPurchase
     */
    protected function setConvertFactorPurchase($convertFactorPurchase)
    {
        $this->convertFactorPurchase = $convertFactorPurchase;
    }

    /**
     *
     * @param mixed $convertedPurchaseQuantity
     */
    protected function setConvertedPurchaseQuantity($convertedPurchaseQuantity)
    {
        $this->convertedPurchaseQuantity = $convertedPurchaseQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardQuantity
     */
    protected function setConvertedStandardQuantity($convertedStandardQuantity)
    {
        $this->convertedStandardQuantity = $convertedStandardQuantity;
    }

    /**
     *
     * @param mixed $convertedStockQuantity
     */
    protected function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardUnitPrice
     */
    protected function setConvertedStandardUnitPrice($convertedStandardUnitPrice)
    {
        $this->convertedStandardUnitPrice = $convertedStandardUnitPrice;
    }

    /**
     *
     * @param mixed $convertedStockUnitPrice
     */
    protected function setConvertedStockUnitPrice($convertedStockUnitPrice)
    {
        $this->convertedStockUnitPrice = $convertedStockUnitPrice;
    }

    /**
     *
     * @param mixed $docQuantity
     */
    protected function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;
    }

    /**
     *
     * @param mixed $docUnit
     */
    protected function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;
    }

    /**
     *
     * @param mixed $docUnitPrice
     */
    protected function setDocUnitPrice($docUnitPrice)
    {
        $this->docUnitPrice = $docUnitPrice;
    }

    /**
     *
     * @param mixed $convertedPurchaseUnitPrice
     */
    protected function setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice)
    {
        $this->convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice;
    }

    /**
     *
     * @param mixed $docType
     */
    protected function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @param mixed $descriptionText
     */
    protected function setDescriptionText($descriptionText)
    {
        $this->descriptionText = $descriptionText;
    }

    /**
     *
     * @param mixed $vendorItemName
     */
    protected function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;
    }

    /**
     *
     * @param mixed $reversalBlocked
     */
    protected function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     *
     * @param mixed $invoice
     */
    protected function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    protected function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @param mixed $prRow
     */
    protected function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $warehouse
     */
    protected function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @param mixed $po
     */
    protected function setPo($po)
    {
        $this->po = $po;
    }

    /**
     *
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @param mixed $docUom
     */
    protected function setDocUom($docUom)
    {
        $this->docUom = $docUom;
    }

    /**
     *
     * @param mixed $docVersion
     */
    protected function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $localUnitPrice
     */
    protected function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;
    }

    /**
     *
     * @param mixed $exwCurrency
     */
    protected function setExwCurrency($exwCurrency)
    {
        $this->exwCurrency = $exwCurrency;
    }

    /**
     *
     * @param mixed $localNetAmount
     */
    protected function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;
    }

    /**
     *
     * @param mixed $localGrossAmount
     */
    protected function setLocalGrossAmount($localGrossAmount)
    {
        $this->localGrossAmount = $localGrossAmount;
    }

    /**
     *
     * @param mixed $transactionType
     */
    protected function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     *
     * @param mixed $isReversed
     */
    protected function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    protected function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @param mixed $glAccount
     */
    protected function setGlAccount($glAccount)
    {
        $this->glAccount = $glAccount;
    }

    /**
     *
     * @param mixed $costCenter
     */
    protected function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    /**
     *
     * @param mixed $standardConvertFactor
     */
    protected function setStandardConvertFactor($standardConvertFactor)
    {
        $this->standardConvertFactor = $standardConvertFactor;
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
}
