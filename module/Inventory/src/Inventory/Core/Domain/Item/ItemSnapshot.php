<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSnapshot extends AbstractValueObject
{

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

    public $itemTypeId;
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
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @return mixed
     */
    public function getItemSku()
    {
        return $this->itemSku;
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
    public function getItemNameForeign()
    {
        return $this->itemNameForeign;
    }

    /**
     * @return mixed
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @return mixed
     */
    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
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
    public function getIsStocked()
    {
        return $this->isStocked;
    }

    /**
     * @return mixed
     */
    public function getIsSaleItem()
    {
        return $this->isSaleItem;
    }

    /**
     * @return mixed
     */
    public function getIsPurchased()
    {
        return $this->isPurchased;
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
    public function getIsSparepart()
    {
        return $this->isSparepart;
    }

    /**
     * @return mixed
     */
    public function getUom()
    {
        return $this->uom;
    }

    /**
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @return mixed
     */
    public function getBarcode39()
    {
        return $this->barcode39;
    }

    /**
     * @return mixed
     */
    public function getBarcode128()
    {
        return $this->barcode128;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @return mixed
     */
    public function getManufacturerCode()
    {
        return $this->manufacturerCode;
    }

    /**
     * @return mixed
     */
    public function getManufacturerCatalog()
    {
        return $this->manufacturerCatalog;
    }

    /**
     * @return mixed
     */
    public function getManufacturerModel()
    {
        return $this->manufacturerModel;
    }

    /**
     * @return mixed
     */
    public function getManufacturerSerial()
    {
        return $this->manufacturerSerial;
    }

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * @return mixed
     */
    public function getLastPurchasePrice()
    {
        return $this->lastPurchasePrice;
    }

    /**
     * @return mixed
     */
    public function getLastPurchaseCurrency()
    {
        return $this->lastPurchaseCurrency;
    }

    /**
     * @return mixed
     */
    public function getLastPurchaseDate()
    {
        return $this->lastPurchaseDate;
    }

    /**
     * @return mixed
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     * @return mixed
     */
    public function getValidFromDate()
    {
        return $this->validFromDate;
    }

    /**
     * @return mixed
     */
    public function getValidToDate()
    {
        return $this->validToDate;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getItemInternalLabel()
    {
        return $this->itemInternalLabel;
    }

    /**
     * @return mixed
     */
    public function getAssetLabel()
    {
        return $this->assetLabel;
    }

    /**
     * @return mixed
     */
    public function getSparepartLabel()
    {
        return $this->sparepartLabel;
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
    public function getLocalAvailabiliy()
    {
        return $this->localAvailabiliy;
    }

    /**
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getChecksum()
    {
        return $this->checksum;
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
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     * @return mixed
     */
    public function getMonitoredBy()
    {
        return $this->monitoredBy;
    }

    /**
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * @return mixed
     */
    public function getRemarksText()
    {
        return $this->remarksText;
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
    public function getItemSku1()
    {
        return $this->itemSku1;
    }

    /**
     * @return mixed
     */
    public function getItemSku2()
    {
        return $this->itemSku2;
    }

    /**
     * @return mixed
     */
    public function getAssetGroup()
    {
        return $this->assetGroup;
    }

    /**
     * @return mixed
     */
    public function getAssetClass()
    {
        return $this->assetClass;
    }

    /**
     * @return mixed
     */
    public function getStockUomConvertFactor()
    {
        return $this->stockUomConvertFactor;
    }

    /**
     * @return mixed
     */
    public function getPurchaseUomConvertFactor()
    {
        return $this->purchaseUomConvertFactor;
    }

    /**
     * @return mixed
     */
    public function getSalesUomConvertFactor()
    {
        return $this->salesUomConvertFactor;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @return mixed
     */
    public function getAvgUnitPrice()
    {
        return $this->avgUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getStandardPrice()
    {
        return $this->standardPrice;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return mixed
     */
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     * @return mixed
     */
    public function getStockUom()
    {
        return $this->stockUom;
    }

    /**
     * @return mixed
     */
    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    /**
     * @return mixed
     */
    public function getPurchaseUom()
    {
        return $this->purchaseUom;
    }

    /**
     * @return mixed
     */
    public function getSalesUom()
    {
        return $this->salesUom;
    }

    /**
     * @return mixed
     */
    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    /**
     * @return mixed
     */
    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }

    /**
     * @return mixed
     */
    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    /**
     * @return mixed
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }

    /**
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * @return mixed
     */
    public function getStandardUom()
    {
        return $this->standardUom;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getLastPrRow()
    {
        return $this->lastPrRow;
    }

    /**
     * @return mixed
     */
    public function getLastPoRow()
    {
        return $this->lastPoRow;
    }

    /**
     * @return mixed
     */
    public function getLastApInvoiceRow()
    {
        return $this->lastApInvoiceRow;
    }

    /**
     * @return mixed
     */
    public function getLastTrxRow()
    {
        return $this->lastTrxRow;
    }

    /**
     * @return mixed
     */
    public function getLastPurchasing()
    {
        return $this->lastPurchasing;
    }

    /**
     * @return mixed
     */
    public function getItemTypeId()
    {
        return $this->itemTypeId;
    }

}