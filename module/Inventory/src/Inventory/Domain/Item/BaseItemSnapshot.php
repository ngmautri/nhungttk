<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseItemSnapshot extends AbstractDTO
{

    // orignal attributes;
    public $id;

    public $warehouseId;

    public $itemSku;

    public $itemName;

    public $itemNameForeign;

    public $itemDescription;

    public $itemType;

    public $itemCategory;

    public $keywords;

    public $isActive;

    public $isStocked;

    public $isSaleItem;

    public $isPurchased;

    public $isFixedAsset;

    public $isSparepart;

    public $uom;

    public $barcode;

    public $barcode39;

    public $barcode128;

    public $status;

    public $createdOn;

    public $manufacturer;

    public $manufacturerCode;

    public $manufacturerCatalog;

    public $manufacturerModel;

    public $manufacturerSerial;

    public $origin;

    public $serialNumber;

    public $lastPurchasePrice;

    public $lastPurchaseCurrency;

    public $lastPurchaseDate;

    public $leadTime;

    public $validFromDate;

    public $validToDate;

    public $location;

    public $itemInternalLabel;

    public $assetLabel;

    public $sparepartLabel;

    public $remarks;

    public $localAvailabiliy;

    public $lastChangeOn;

    public $token;

    public $checksum;

    public $currentState;

    public $docNumber;

    public $monitoredBy;

    public $sysNumber;

    public $remarksText;

    public $revisionNo;

    public $itemSku1;

    public $itemSku2;

    public $assetGroup;

    public $assetClass;

    public $stockUomConvertFactor;

    public $purchaseUomConvertFactor;

    public $salesUomConvertFactor;

    public $capacity;

    public $avgUnitPrice;

    public $standardPrice;

    public $uuid;

    public $itemTypeId;

    public $createdBy;

    public $itemGroup;

    public $stockUom;

    public $cogsAccount;

    public $purchaseUom;

    public $salesUom;

    public $inventoryAccount;

    public $expenseAccount;

    public $revenueAccount;

    public $defaultWarehouse;

    public $lastChangeBy;

    public $standardUom;

    public $company;

    public $lastPrRow;

    public $lastPoRow;

    public $lastApInvoiceRow;

    public $lastTrxRow;

    public $lastPurchasing;

    public $isModel;

    public $canOrder;

    public $modelDetail;

    public $hsCode;

    public $hsCodeDescription;

    public $standardWeightInKg;

    public $standardVolumnInM3;

    public $itemName1;

    public $itemName2;

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
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     *
     * @param mixed $itemSku
     */
    public function setItemSku($itemSku)
    {
        $this->itemSku = $itemSku;
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
     * @param mixed $itemNameForeign
     */
    public function setItemNameForeign($itemNameForeign)
    {
        $this->itemNameForeign = $itemNameForeign;
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
     * @param mixed $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

    /**
     *
     * @param mixed $itemCategory
     */
    public function setItemCategory($itemCategory)
    {
        $this->itemCategory = $itemCategory;
    }

    /**
     *
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
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
     * @param mixed $isStocked
     */
    public function setIsStocked($isStocked)
    {
        $this->isStocked = $isStocked;
    }

    /**
     *
     * @param mixed $isSaleItem
     */
    public function setIsSaleItem($isSaleItem)
    {
        $this->isSaleItem = $isSaleItem;
    }

    /**
     *
     * @param mixed $isPurchased
     */
    public function setIsPurchased($isPurchased)
    {
        $this->isPurchased = $isPurchased;
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
     * @param mixed $isSparepart
     */
    public function setIsSparepart($isSparepart)
    {
        $this->isSparepart = $isSparepart;
    }

    /**
     *
     * @param mixed $uom
     */
    public function setUom($uom)
    {
        $this->uom = $uom;
    }

    /**
     *
     * @param mixed $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     *
     * @param mixed $barcode39
     */
    public function setBarcode39($barcode39)
    {
        $this->barcode39 = $barcode39;
    }

    /**
     *
     * @param mixed $barcode128
     */
    public function setBarcode128($barcode128)
    {
        $this->barcode128 = $barcode128;
    }

    /**
     *
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     *
     * @param mixed $manufacturerCode
     */
    public function setManufacturerCode($manufacturerCode)
    {
        $this->manufacturerCode = $manufacturerCode;
    }

    /**
     *
     * @param mixed $manufacturerCatalog
     */
    public function setManufacturerCatalog($manufacturerCatalog)
    {
        $this->manufacturerCatalog = $manufacturerCatalog;
    }

    /**
     *
     * @param mixed $manufacturerModel
     */
    public function setManufacturerModel($manufacturerModel)
    {
        $this->manufacturerModel = $manufacturerModel;
    }

    /**
     *
     * @param mixed $manufacturerSerial
     */
    public function setManufacturerSerial($manufacturerSerial)
    {
        $this->manufacturerSerial = $manufacturerSerial;
    }

    /**
     *
     * @param mixed $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     *
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     *
     * @param mixed $lastPurchasePrice
     */
    public function setLastPurchasePrice($lastPurchasePrice)
    {
        $this->lastPurchasePrice = $lastPurchasePrice;
    }

    /**
     *
     * @param mixed $lastPurchaseCurrency
     */
    public function setLastPurchaseCurrency($lastPurchaseCurrency)
    {
        $this->lastPurchaseCurrency = $lastPurchaseCurrency;
    }

    /**
     *
     * @param mixed $lastPurchaseDate
     */
    public function setLastPurchaseDate($lastPurchaseDate)
    {
        $this->lastPurchaseDate = $lastPurchaseDate;
    }

    /**
     *
     * @param mixed $leadTime
     */
    public function setLeadTime($leadTime)
    {
        $this->leadTime = $leadTime;
    }

    /**
     *
     * @param mixed $validFromDate
     */
    public function setValidFromDate($validFromDate)
    {
        $this->validFromDate = $validFromDate;
    }

    /**
     *
     * @param mixed $validToDate
     */
    public function setValidToDate($validToDate)
    {
        $this->validToDate = $validToDate;
    }

    /**
     *
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @param mixed $itemInternalLabel
     */
    public function setItemInternalLabel($itemInternalLabel)
    {
        $this->itemInternalLabel = $itemInternalLabel;
    }

    /**
     *
     * @param mixed $assetLabel
     */
    public function setAssetLabel($assetLabel)
    {
        $this->assetLabel = $assetLabel;
    }

    /**
     *
     * @param mixed $sparepartLabel
     */
    public function setSparepartLabel($sparepartLabel)
    {
        $this->sparepartLabel = $sparepartLabel;
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
     * @param mixed $localAvailabiliy
     */
    public function setLocalAvailabiliy($localAvailabiliy)
    {
        $this->localAvailabiliy = $localAvailabiliy;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
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
     * @param mixed $docNumber
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;
    }

    /**
     *
     * @param mixed $monitoredBy
     */
    public function setMonitoredBy($monitoredBy)
    {
        $this->monitoredBy = $monitoredBy;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $remarksText
     */
    public function setRemarksText($remarksText)
    {
        $this->remarksText = $remarksText;
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
     * @param mixed $itemSku1
     */
    public function setItemSku1($itemSku1)
    {
        $this->itemSku1 = $itemSku1;
    }

    /**
     *
     * @param mixed $itemSku2
     */
    public function setItemSku2($itemSku2)
    {
        $this->itemSku2 = $itemSku2;
    }

    /**
     *
     * @param mixed $assetGroup
     */
    public function setAssetGroup($assetGroup)
    {
        $this->assetGroup = $assetGroup;
    }

    /**
     *
     * @param mixed $assetClass
     */
    public function setAssetClass($assetClass)
    {
        $this->assetClass = $assetClass;
    }

    /**
     *
     * @param mixed $stockUomConvertFactor
     */
    public function setStockUomConvertFactor($stockUomConvertFactor)
    {
        $this->stockUomConvertFactor = $stockUomConvertFactor;
    }

    /**
     *
     * @param mixed $purchaseUomConvertFactor
     */
    public function setPurchaseUomConvertFactor($purchaseUomConvertFactor)
    {
        $this->purchaseUomConvertFactor = $purchaseUomConvertFactor;
    }

    /**
     *
     * @param mixed $salesUomConvertFactor
     */
    public function setSalesUomConvertFactor($salesUomConvertFactor)
    {
        $this->salesUomConvertFactor = $salesUomConvertFactor;
    }

    /**
     *
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     *
     * @param mixed $avgUnitPrice
     */
    public function setAvgUnitPrice($avgUnitPrice)
    {
        $this->avgUnitPrice = $avgUnitPrice;
    }

    /**
     *
     * @param mixed $standardPrice
     */
    public function setStandardPrice($standardPrice)
    {
        $this->standardPrice = $standardPrice;
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
     * @param mixed $itemTypeId
     */
    public function setItemTypeId($itemTypeId)
    {
        $this->itemTypeId = $itemTypeId;
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
     * @param mixed $itemGroup
     */
    public function setItemGroup($itemGroup)
    {
        $this->itemGroup = $itemGroup;
    }

    /**
     *
     * @param mixed $stockUom
     */
    public function setStockUom($stockUom)
    {
        $this->stockUom = $stockUom;
    }

    /**
     *
     * @param mixed $cogsAccount
     */
    public function setCogsAccount($cogsAccount)
    {
        $this->cogsAccount = $cogsAccount;
    }

    /**
     *
     * @param mixed $purchaseUom
     */
    public function setPurchaseUom($purchaseUom)
    {
        $this->purchaseUom = $purchaseUom;
    }

    /**
     *
     * @param mixed $salesUom
     */
    public function setSalesUom($salesUom)
    {
        $this->salesUom = $salesUom;
    }

    /**
     *
     * @param mixed $inventoryAccount
     */
    public function setInventoryAccount($inventoryAccount)
    {
        $this->inventoryAccount = $inventoryAccount;
    }

    /**
     *
     * @param mixed $expenseAccount
     */
    public function setExpenseAccount($expenseAccount)
    {
        $this->expenseAccount = $expenseAccount;
    }

    /**
     *
     * @param mixed $revenueAccount
     */
    public function setRevenueAccount($revenueAccount)
    {
        $this->revenueAccount = $revenueAccount;
    }

    /**
     *
     * @param mixed $defaultWarehouse
     */
    public function setDefaultWarehouse($defaultWarehouse)
    {
        $this->defaultWarehouse = $defaultWarehouse;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $standardUom
     */
    public function setStandardUom($standardUom)
    {
        $this->standardUom = $standardUom;
    }

    /**
     *
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     *
     * @param mixed $lastPrRow
     */
    public function setLastPrRow($lastPrRow)
    {
        $this->lastPrRow = $lastPrRow;
    }

    /**
     *
     * @param mixed $lastPoRow
     */
    public function setLastPoRow($lastPoRow)
    {
        $this->lastPoRow = $lastPoRow;
    }

    /**
     *
     * @param mixed $lastApInvoiceRow
     */
    public function setLastApInvoiceRow($lastApInvoiceRow)
    {
        $this->lastApInvoiceRow = $lastApInvoiceRow;
    }

    /**
     *
     * @param mixed $lastTrxRow
     */
    public function setLastTrxRow($lastTrxRow)
    {
        $this->lastTrxRow = $lastTrxRow;
    }

    /**
     *
     * @param mixed $lastPurchasing
     */
    public function setLastPurchasing($lastPurchasing)
    {
        $this->lastPurchasing = $lastPurchasing;
    }

    /**
     *
     * @param mixed $isModel
     */
    public function setIsModel($isModel)
    {
        $this->isModel = $isModel;
    }

    /**
     *
     * @param mixed $canOrder
     */
    public function setCanOrder($canOrder)
    {
        $this->canOrder = $canOrder;
    }

    /**
     *
     * @param mixed $modelDetail
     */
    public function setModelDetail($modelDetail)
    {
        $this->modelDetail = $modelDetail;
    }

    /**
     *
     * @param mixed $hsCode
     */
    public function setHsCode($hsCode)
    {
        $this->hsCode = $hsCode;
    }

    /**
     *
     * @param mixed $hsCodeDescription
     */
    public function setHsCodeDescription($hsCodeDescription)
    {
        $this->hsCodeDescription = $hsCodeDescription;
    }

    /**
     *
     * @param mixed $standardWeightInKg
     */
    public function setStandardWeightInKg($standardWeightInKg)
    {
        $this->standardWeightInKg = $standardWeightInKg;
    }

    /**
     *
     * @param mixed $standardVolumnInM3
     */
    public function setStandardVolumnInM3($standardVolumnInM3)
    {
        $this->standardVolumnInM3 = $standardVolumnInM3;
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
     * @param mixed $itemName2
     */
    public function setItemName2($itemName2)
    {
        $this->itemName2 = $itemName2;
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
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSku()
    {
        return $this->itemSku;
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
    public function getItemNameForeign()
    {
        return $this->itemNameForeign;
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
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     *
     * @return mixed
     */
    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    /**
     *
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
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
    public function getIsStocked()
    {
        return $this->isStocked;
    }

    /**
     *
     * @return mixed
     */
    public function getIsSaleItem()
    {
        return $this->isSaleItem;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPurchased()
    {
        return $this->isPurchased;
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
    public function getIsSparepart()
    {
        return $this->isSparepart;
    }

    /**
     *
     * @return mixed
     */
    public function getUom()
    {
        return $this->uom;
    }

    /**
     *
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     *
     * @return mixed
     */
    public function getBarcode39()
    {
        return $this->barcode39;
    }

    /**
     *
     * @return mixed
     */
    public function getBarcode128()
    {
        return $this->barcode128;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     *
     * @return mixed
     */
    public function getManufacturerCode()
    {
        return $this->manufacturerCode;
    }

    /**
     *
     * @return mixed
     */
    public function getManufacturerCatalog()
    {
        return $this->manufacturerCatalog;
    }

    /**
     *
     * @return mixed
     */
    public function getManufacturerModel()
    {
        return $this->manufacturerModel;
    }

    /**
     *
     * @return mixed
     */
    public function getManufacturerSerial()
    {
        return $this->manufacturerSerial;
    }

    /**
     *
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPurchasePrice()
    {
        return $this->lastPurchasePrice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPurchaseCurrency()
    {
        return $this->lastPurchaseCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPurchaseDate()
    {
        return $this->lastPurchaseDate;
    }

    /**
     *
     * @return mixed
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     *
     * @return mixed
     */
    public function getValidFromDate()
    {
        return $this->validFromDate;
    }

    /**
     *
     * @return mixed
     */
    public function getValidToDate()
    {
        return $this->validToDate;
    }

    /**
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @return mixed
     */
    public function getItemInternalLabel()
    {
        return $this->itemInternalLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getAssetLabel()
    {
        return $this->assetLabel;
    }

    /**
     *
     * @return mixed
     */
    public function getSparepartLabel()
    {
        return $this->sparepartLabel;
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
    public function getLocalAvailabiliy()
    {
        return $this->localAvailabiliy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getChecksum()
    {
        return $this->checksum;
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
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getMonitoredBy()
    {
        return $this->monitoredBy;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarksText()
    {
        return $this->remarksText;
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
    public function getItemSku1()
    {
        return $this->itemSku1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSku2()
    {
        return $this->itemSku2;
    }

    /**
     *
     * @return mixed
     */
    public function getAssetGroup()
    {
        return $this->assetGroup;
    }

    /**
     *
     * @return mixed
     */
    public function getAssetClass()
    {
        return $this->assetClass;
    }

    /**
     *
     * @return mixed
     */
    public function getStockUomConvertFactor()
    {
        return $this->stockUomConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getPurchaseUomConvertFactor()
    {
        return $this->purchaseUomConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getSalesUomConvertFactor()
    {
        return $this->salesUomConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     *
     * @return mixed
     */
    public function getAvgUnitPrice()
    {
        return $this->avgUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardPrice()
    {
        return $this->standardPrice;
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
    public function getItemTypeId()
    {
        return $this->itemTypeId;
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
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     *
     * @return mixed
     */
    public function getStockUom()
    {
        return $this->stockUom;
    }

    /**
     *
     * @return mixed
     */
    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getPurchaseUom()
    {
        return $this->purchaseUom;
    }

    /**
     *
     * @return mixed
     */
    public function getSalesUom()
    {
        return $this->salesUom;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUom()
    {
        return $this->standardUom;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPrRow()
    {
        return $this->lastPrRow;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPoRow()
    {
        return $this->lastPoRow;
    }

    /**
     *
     * @return mixed
     */
    public function getLastApInvoiceRow()
    {
        return $this->lastApInvoiceRow;
    }

    /**
     *
     * @return mixed
     */
    public function getLastTrxRow()
    {
        return $this->lastTrxRow;
    }

    /**
     *
     * @return mixed
     */
    public function getLastPurchasing()
    {
        return $this->lastPurchasing;
    }

    /**
     *
     * @return mixed
     */
    public function getIsModel()
    {
        return $this->isModel;
    }

    /**
     *
     * @return mixed
     */
    public function getCanOrder()
    {
        return $this->canOrder;
    }

    /**
     *
     * @return mixed
     */
    public function getModelDetail()
    {
        return $this->modelDetail;
    }

    /**
     *
     * @return mixed
     */
    public function getHsCode()
    {
        return $this->hsCode;
    }

    /**
     *
     * @return mixed
     */
    public function getHsCodeDescription()
    {
        return $this->hsCodeDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardWeightInKg()
    {
        return $this->standardWeightInKg;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardVolumnInM3()
    {
        return $this->standardVolumnInM3;
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
    public function getItemName2()
    {
        return $this->itemName2;
    }
}