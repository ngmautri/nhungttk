<?php
namespace Procure\Domain;

/**
 * Abstract Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseRow extends AbstractRow
{

    protected $standardQuantity;

    protected $standardUnitPriceInDocCurrency;

    protected $standardUnitPriceInLocCurrency;

    protected $docQuantityObject;

    protected $baseUomPair;

    protected $docUnitPriceObject;

    protected $baseDocUnitPriceObject;

    protected $currencyPair;

    protected $localUnitPriceObject;

    protected $baseLocalUnitPriceObject;

    protected $localStandardUnitPrice;

    // Row Details
    // ====================
    protected $createdByName;

    protected $lastChangeByName;

    protected $glAccountName;

    protected $glAccountNumber;

    protected $glAccountType;

    protected $costCenterName;

    protected $discountAmount;

    protected $convertedDocQuantity;

    protected $convertedDocUnitPrice;

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

    protected $docRevisionNo;

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

    protected $itemDescription;

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

    protected $prDepartment;

    protected $prDepartmentName;

    protected $prWarehouse;

    protected $prWarehouseName;

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
}
