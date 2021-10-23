<?php
namespace Procure\Domain;

/**
 * Abstract Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class BaseRow extends AbstractRow
{

    /*
     * |=============================
     * | Quantity
     * |
     * |=============================
     */

    // Quotation
    protected $qoQuantity;

    protected $standardQoQuantity;

    protected $postedQoQuantity;

    protected $postedStandardQoQuantity;

    // PO
    protected $draftPoQuantity;

    protected $standardPoQuantity;

    protected $postedPoQuantity;

    protected $postedStandardPoQuantity;

    // PO-GR
    protected $draftGrQuantity;

    protected $standardGrQuantity;

    protected $postedGrQuantity;

    protected $postedStandardGrQuantity;

    // PO-RETURN
    protected $draftReturnQuantity;

    protected $standardReturnQuantity;

    protected $postedReturnQuantity;

    protected $postedStandardReturnQuantity;

    // STOCK-GR
    protected $draftStockQrQuantity;

    protected $standardStockQrQuantity;

    protected $postedStockQrQuantity;

    protected $postedStandardStockQrQuantity;

    // STOCK-RETURN
    protected $draftStockReturnQuantity;

    protected $standardStockReturnQuantity;

    protected $postedStockReturnQuantity;

    protected $postedStandardStockReturnQuantity;

    // AP
    protected $draftApQuantity;

    protected $standardApQuantity;

    protected $postedApQuantity;

    protected $postedStandardApQuantity;

    /*
     * |=============================
     * | Other
     * |
     * |=============================
     */
    protected $constructedFromDB = FALSE;

    protected $createdVO = FALSE;

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

    /*
     * |=============================
     * | Row Details
     * |
     * |=============================
     */
    protected $createdByName;

    protected $lastChangeByName;

    protected $glAccountName;

    protected $glAccountNumber;

    protected $glAccountType;

    protected $costCenterName;

    protected $discountAmount;

    protected $convertedDocQuantity;

    protected $convertedDocUnitPrice;

    /*
     * |=============================
     * | Doc Details
     * |
     * |=============================
     */
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

    /*
     * |=============================
     * | Warehouse Details
     * |
     * |=============================
     */
    protected $docWarehouseName;

    protected $docWarehouseCode;

    protected $warehouseName;

    protected $warehouseCode;

    /*
     * |=============================
     * | UOM Details
     * |
     * |=============================
     */
    protected $docUomName;

    protected $docUomCode;

    protected $docUomDescription;

    /*
     * |=============================
     * | Item Details
     * |
     * |=============================
     */
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

    /*
     * |=============================
     * | Item Group Detail
     * |
     * |=============================
     */
    protected $itemInventoryGL;

    protected $itemCogsGL;

    protected $itemCostCenter;

    /*
     * |=============================
     * | PR Detail
     * |
     * |=============================
     */
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

    /*
     * |=============================
     * | Document Relationship
     * |
     * |=============================
     */
    protected $prDocId;

    protected $prDocToken;

    protected $grDocId;

    protected $grDocToken;

    protected $grSysNumber;

    protected $apDocId;

    protected $apDocToken;

    protected $apSysNumber;

    /*
     * |=============================
     * | GETTER : public
     * | SEETER : procteced
     * |
     * |=============================
     */
    /**
     *
     * @param mixed $qoQuantity
     */
    protected function setQoQuantity($qoQuantity)
    {
        $this->qoQuantity = $qoQuantity;
    }

    /**
     *
     * @param mixed $standardQoQuantity
     */
    protected function setStandardQoQuantity($standardQoQuantity)
    {
        $this->standardQoQuantity = $standardQoQuantity;
    }

    /**
     *
     * @param mixed $postedQoQuantity
     */
    protected function setPostedQoQuantity($postedQoQuantity)
    {
        $this->postedQoQuantity = $postedQoQuantity;
    }

    /**
     *
     * @param mixed $postedStandardQoQuantity
     */
    protected function setPostedStandardQoQuantity($postedStandardQoQuantity)
    {
        $this->postedStandardQoQuantity = $postedStandardQoQuantity;
    }

    /**
     *
     * @param mixed $draftPoQuantity
     */
    protected function setDraftPoQuantity($draftPoQuantity)
    {
        $this->draftPoQuantity = $draftPoQuantity;
    }

    /**
     *
     * @param mixed $standardPoQuantity
     */
    protected function setStandardPoQuantity($standardPoQuantity)
    {
        $this->standardPoQuantity = $standardPoQuantity;
    }

    /**
     *
     * @param mixed $postedPoQuantity
     */
    protected function setPostedPoQuantity($postedPoQuantity)
    {
        $this->postedPoQuantity = $postedPoQuantity;
    }

    /**
     *
     * @param mixed $postedStandardPoQuantity
     */
    protected function setPostedStandardPoQuantity($postedStandardPoQuantity)
    {
        $this->postedStandardPoQuantity = $postedStandardPoQuantity;
    }

    /**
     *
     * @param mixed $draftGrQuantity
     */
    protected function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     *
     * @param mixed $standardGrQuantity
     */
    protected function setStandardGrQuantity($standardGrQuantity)
    {
        $this->standardGrQuantity = $standardGrQuantity;
    }

    /**
     *
     * @param mixed $postedGrQuantity
     */
    protected function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     *
     * @param mixed $postedStandardGrQuantity
     */
    protected function setPostedStandardGrQuantity($postedStandardGrQuantity)
    {
        $this->postedStandardGrQuantity = $postedStandardGrQuantity;
    }

    /**
     *
     * @param mixed $draftReturnQuantity
     */
    protected function setDraftReturnQuantity($draftReturnQuantity)
    {
        $this->draftReturnQuantity = $draftReturnQuantity;
    }

    /**
     *
     * @param mixed $standardReturnQuantity
     */
    protected function setStandardReturnQuantity($standardReturnQuantity)
    {
        $this->standardReturnQuantity = $standardReturnQuantity;
    }

    /**
     *
     * @param mixed $postedReturnQuantity
     */
    protected function setPostedReturnQuantity($postedReturnQuantity)
    {
        $this->postedReturnQuantity = $postedReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStandardReturnQuantity
     */
    protected function setPostedStandardReturnQuantity($postedStandardReturnQuantity)
    {
        $this->postedStandardReturnQuantity = $postedStandardReturnQuantity;
    }

    /**
     *
     * @param mixed $draftStockQrQuantity
     */
    protected function setDraftStockQrQuantity($draftStockQrQuantity)
    {
        $this->draftStockQrQuantity = $draftStockQrQuantity;
    }

    /**
     *
     * @param mixed $standardStockQrQuantity
     */
    protected function setStandardStockQrQuantity($standardStockQrQuantity)
    {
        $this->standardStockQrQuantity = $standardStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStockQrQuantity
     */
    protected function setPostedStockQrQuantity($postedStockQrQuantity)
    {
        $this->postedStockQrQuantity = $postedStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStandardStockQrQuantity
     */
    protected function setPostedStandardStockQrQuantity($postedStandardStockQrQuantity)
    {
        $this->postedStandardStockQrQuantity = $postedStandardStockQrQuantity;
    }

    /**
     *
     * @param mixed $draftStockReturnQuantity
     */
    protected function setDraftStockReturnQuantity($draftStockReturnQuantity)
    {
        $this->draftStockReturnQuantity = $draftStockReturnQuantity;
    }

    /**
     *
     * @param mixed $standardStockReturnQuantity
     */
    protected function setStandardStockReturnQuantity($standardStockReturnQuantity)
    {
        $this->standardStockReturnQuantity = $standardStockReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStockReturnQuantity
     */
    protected function setPostedStockReturnQuantity($postedStockReturnQuantity)
    {
        $this->postedStockReturnQuantity = $postedStockReturnQuantity;
    }

    /**
     *
     * @param mixed $postedStandardStockReturnQuantity
     */
    protected function setPostedStandardStockReturnQuantity($postedStandardStockReturnQuantity)
    {
        $this->postedStandardStockReturnQuantity = $postedStandardStockReturnQuantity;
    }

    /**
     *
     * @param mixed $draftApQuantity
     */
    protected function setDraftApQuantity($draftApQuantity)
    {
        $this->draftApQuantity = $draftApQuantity;
    }

    /**
     *
     * @param mixed $standardApQuantity
     */
    protected function setStandardApQuantity($standardApQuantity)
    {
        $this->standardApQuantity = $standardApQuantity;
    }

    /**
     *
     * @param mixed $postedApQuantity
     */
    protected function setPostedApQuantity($postedApQuantity)
    {
        $this->postedApQuantity = $postedApQuantity;
    }

    /**
     *
     * @param mixed $postedStandardApQuantity
     */
    protected function setPostedStandardApQuantity($postedStandardApQuantity)
    {
        $this->postedStandardApQuantity = $postedStandardApQuantity;
    }

    /**
     *
     * @param boolean $constructedFromDB
     */
    protected function setConstructedFromDB($constructedFromDB)
    {
        $this->constructedFromDB = $constructedFromDB;
    }

    /**
     *
     * @param mixed $standardQuantity
     */
    protected function setStandardQuantity($standardQuantity)
    {
        $this->standardQuantity = $standardQuantity;
    }

    /**
     *
     * @param mixed $standardUnitPriceInDocCurrency
     */
    protected function setStandardUnitPriceInDocCurrency($standardUnitPriceInDocCurrency)
    {
        $this->standardUnitPriceInDocCurrency = $standardUnitPriceInDocCurrency;
    }

    /**
     *
     * @param mixed $standardUnitPriceInLocCurrency
     */
    protected function setStandardUnitPriceInLocCurrency($standardUnitPriceInLocCurrency)
    {
        $this->standardUnitPriceInLocCurrency = $standardUnitPriceInLocCurrency;
    }

    /**
     *
     * @param mixed $docQuantityObject
     */
    protected function setDocQuantityObject($docQuantityObject)
    {
        $this->docQuantityObject = $docQuantityObject;
    }

    /**
     *
     * @param mixed $baseUomPair
     */
    protected function setBaseUomPair($baseUomPair)
    {
        $this->baseUomPair = $baseUomPair;
    }

    /**
     *
     * @param mixed $docUnitPriceObject
     */
    protected function setDocUnitPriceObject($docUnitPriceObject)
    {
        $this->docUnitPriceObject = $docUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseDocUnitPriceObject
     */
    protected function setBaseDocUnitPriceObject($baseDocUnitPriceObject)
    {
        $this->baseDocUnitPriceObject = $baseDocUnitPriceObject;
    }

    /**
     *
     * @param mixed $currencyPair
     */
    protected function setCurrencyPair($currencyPair)
    {
        $this->currencyPair = $currencyPair;
    }

    /**
     *
     * @param mixed $localUnitPriceObject
     */
    protected function setLocalUnitPriceObject($localUnitPriceObject)
    {
        $this->localUnitPriceObject = $localUnitPriceObject;
    }

    /**
     *
     * @param mixed $baseLocalUnitPriceObject
     */
    protected function setBaseLocalUnitPriceObject($baseLocalUnitPriceObject)
    {
        $this->baseLocalUnitPriceObject = $baseLocalUnitPriceObject;
    }

    /**
     *
     * @param mixed $localStandardUnitPrice
     */
    protected function setLocalStandardUnitPrice($localStandardUnitPrice)
    {
        $this->localStandardUnitPrice = $localStandardUnitPrice;
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
     * @param mixed $discountAmount
     */
    protected function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     *
     * @param mixed $convertedDocQuantity
     */
    protected function setConvertedDocQuantity($convertedDocQuantity)
    {
        $this->convertedDocQuantity = $convertedDocQuantity;
    }

    /**
     *
     * @param mixed $convertedDocUnitPrice
     */
    protected function setConvertedDocUnitPrice($convertedDocUnitPrice)
    {
        $this->convertedDocUnitPrice = $convertedDocUnitPrice;
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
     * @param mixed $docRevisionNo
     */
    protected function setDocRevisionNo($docRevisionNo)
    {
        $this->docRevisionNo = $docRevisionNo;
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
     * @param mixed $itemModel
     */
    protected function setItemModel($itemModel)
    {
        $this->itemModel = $itemModel;
    }

    /**
     *
     * @param mixed $itemSerial
     */
    protected function setItemSerial($itemSerial)
    {
        $this->itemSerial = $itemSerial;
    }

    /**
     *
     * @param mixed $itemDefaultManufacturer
     */
    protected function setItemDefaultManufacturer($itemDefaultManufacturer)
    {
        $this->itemDefaultManufacturer = $itemDefaultManufacturer;
    }

    /**
     *
     * @param mixed $itemManufacturerModel
     */
    protected function setItemManufacturerModel($itemManufacturerModel)
    {
        $this->itemManufacturerModel = $itemManufacturerModel;
    }

    /**
     *
     * @param mixed $itemManufacturerSerial
     */
    protected function setItemManufacturerSerial($itemManufacturerSerial)
    {
        $this->itemManufacturerSerial = $itemManufacturerSerial;
    }

    /**
     *
     * @param mixed $itemManufacturerCode
     */
    protected function setItemManufacturerCode($itemManufacturerCode)
    {
        $this->itemManufacturerCode = $itemManufacturerCode;
    }

    /**
     *
     * @param mixed $itemKeywords
     */
    protected function setItemKeywords($itemKeywords)
    {
        $this->itemKeywords = $itemKeywords;
    }

    /**
     *
     * @param mixed $itemAssetLabel
     */
    protected function setItemAssetLabel($itemAssetLabel)
    {
        $this->itemAssetLabel = $itemAssetLabel;
    }

    /**
     *
     * @param mixed $itemAssetLabel1
     */
    protected function setItemAssetLabel1($itemAssetLabel1)
    {
        $this->itemAssetLabel1 = $itemAssetLabel1;
    }

    /**
     *
     * @param mixed $itemDescription
     */
    protected function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;
    }

    /**
     *
     * @param mixed $itemInventoryGL
     */
    protected function setItemInventoryGL($itemInventoryGL)
    {
        $this->itemInventoryGL = $itemInventoryGL;
    }

    /**
     *
     * @param mixed $itemCogsGL
     */
    protected function setItemCogsGL($itemCogsGL)
    {
        $this->itemCogsGL = $itemCogsGL;
    }

    /**
     *
     * @param mixed $itemCostCenter
     */
    protected function setItemCostCenter($itemCostCenter)
    {
        $this->itemCostCenter = $itemCostCenter;
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
     * @param mixed $prDepartment
     */
    protected function setPrDepartment($prDepartment)
    {
        $this->prDepartment = $prDepartment;
    }

    /**
     *
     * @param mixed $prDepartmentName
     */
    protected function setPrDepartmentName($prDepartmentName)
    {
        $this->prDepartmentName = $prDepartmentName;
    }

    /**
     *
     * @param mixed $prWarehouse
     */
    protected function setPrWarehouse($prWarehouse)
    {
        $this->prWarehouse = $prWarehouse;
    }

    /**
     *
     * @param mixed $prWarehouseName
     */
    protected function setPrWarehouseName($prWarehouseName)
    {
        $this->prWarehouseName = $prWarehouseName;
    }

    /**
     *
     * @param mixed $prDocId
     */
    protected function setPrDocId($prDocId)
    {
        $this->prDocId = $prDocId;
    }

    /**
     *
     * @param mixed $prDocToken
     */
    protected function setPrDocToken($prDocToken)
    {
        $this->prDocToken = $prDocToken;
    }

    /**
     *
     * @param mixed $grDocId
     */
    protected function setGrDocId($grDocId)
    {
        $this->grDocId = $grDocId;
    }

    /**
     *
     * @param mixed $grDocToken
     */
    protected function setGrDocToken($grDocToken)
    {
        $this->grDocToken = $grDocToken;
    }

    /**
     *
     * @param mixed $grSysNumber
     */
    protected function setGrSysNumber($grSysNumber)
    {
        $this->grSysNumber = $grSysNumber;
    }

    /**
     *
     * @param mixed $apDocId
     */
    protected function setApDocId($apDocId)
    {
        $this->apDocId = $apDocId;
    }

    /**
     *
     * @param mixed $apDocToken
     */
    protected function setApDocToken($apDocToken)
    {
        $this->apDocToken = $apDocToken;
    }

    /**
     *
     * @param mixed $apSysNumber
     */
    protected function setApSysNumber($apSysNumber)
    {
        $this->apSysNumber = $apSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getQoQuantity()
    {
        return $this->qoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQoQuantity()
    {
        return $this->standardQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedQoQuantity()
    {
        return $this->postedQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardQoQuantity()
    {
        return $this->postedStandardQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftPoQuantity()
    {
        return $this->draftPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardPoQuantity()
    {
        return $this->standardPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedPoQuantity()
    {
        return $this->postedPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardPoQuantity()
    {
        return $this->postedStandardPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardGrQuantity()
    {
        return $this->standardGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardGrQuantity()
    {
        return $this->postedStandardGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftReturnQuantity()
    {
        return $this->draftReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardReturnQuantity()
    {
        return $this->standardReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedReturnQuantity()
    {
        return $this->postedReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardReturnQuantity()
    {
        return $this->postedStandardReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockQrQuantity()
    {
        return $this->draftStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardStockQrQuantity()
    {
        return $this->standardStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockQrQuantity()
    {
        return $this->postedStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardStockQrQuantity()
    {
        return $this->postedStandardStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockReturnQuantity()
    {
        return $this->draftStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardStockReturnQuantity()
    {
        return $this->standardStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockReturnQuantity()
    {
        return $this->postedStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardStockReturnQuantity()
    {
        return $this->postedStandardStockReturnQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftApQuantity()
    {
        return $this->draftApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardApQuantity()
    {
        return $this->standardApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedApQuantity()
    {
        return $this->postedApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardApQuantity()
    {
        return $this->postedStandardApQuantity;
    }

    /**
     *
     * @return <boolean, boolean>
     */
    public function getConstructedFromDB()
    {
        return $this->constructedFromDB;
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
    public function getCurrencyPair()
    {
        return $this->currencyPair;
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
    public function getPrDocId()
    {
        return $this->prDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getPrDocToken()
    {
        return $this->prDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDocId()
    {
        return $this->grDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDocToken()
    {
        return $this->grDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrSysNumber()
    {
        return $this->grSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getApDocId()
    {
        return $this->apDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getApDocToken()
    {
        return $this->apDocToken;
    }

    /**
     *
     * @return mixed
     */
    public function getApSysNumber()
    {
        return $this->apSysNumber;
    }

    /**
     *
     * @return boolean
     */
    public function getCreatedVO()
    {
        return $this->createdVO;
    }

    /**
     *
     * @param boolean $createdVO
     */
    protected function setCreatedVO($createdVO)
    {
        $this->createdVO = $createdVO;
    }
}
