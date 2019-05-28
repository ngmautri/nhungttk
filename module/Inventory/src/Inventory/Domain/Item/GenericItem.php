<?php
namespace Inventory\Domain\Item;

use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Domain\Exception\InvalidArgumentException;
use Application\Domain\Shared\AbstractEntity;
use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericItem extends AbstractEntity
{

    protected $id;

    protected $warehouseId;

    protected $itemSku;

    protected $itemName;

    protected $itemNameForeign;

    protected $itemDescription;

    protected $itemType;

    protected $itemCategory;

    protected $keywords;

    protected $isActive;

    protected $isStocked;

    protected $isSaleItem;

    protected $isPurchased;

    protected $isFixedAsset;

    protected $isSparepart;

    protected $uom;

    protected $barcode;

    protected $barcode39;

    protected $barcode128;

    protected $status;

    protected $createdOn;

    protected $manufacturer;

    protected $manufacturerCode;

    protected $manufacturerCatalog;

    protected $manufacturerModel;

    protected $manufacturerSerial;

    protected $origin;

    protected $serialNumber;

    protected $lastPurchasePrice;

    protected $lastPurchaseCurrency;

    protected $lastPurchaseDate;

    protected $leadTime;

    protected $validFromDate;

    protected $validToDate;

    protected $location;

    protected $itemInternalLabel;

    protected $assetLabel;

    protected $sparepartLabel;

    protected $remarks;

    protected $localAvailabiliy;

    protected $lastChangeOn;

    protected $token;

    protected $checksum;

    protected $currentState;

    protected $docNumber;

    protected $monitoredBy;

    protected $sysNumber;

    protected $remarksText;

    protected $revisionNo;

    protected $itemSku1;

    protected $itemSku2;

    protected $assetGroup;

    protected $assetClass;

    protected $stockUomConvertFactor;

    protected $purchaseUomConvertFactor;

    protected $salesUomConvertFactor;

    protected $capacity;

    protected $avgUnitPrice;

    protected $standardPrice;

    protected $uuid;

    protected $itemTypeId;

    protected $createdBy;

    protected $itemGroup;

    protected $stockUom;

    protected $cogsAccount;

    protected $purchaseUom;

    protected $salesUom;

    protected $inventoryAccount;

    protected $expenseAccount;

    protected $revenueAccount;

    protected $defaultWarehouse;

    protected $lastChangeBy;

    protected $standardUom;

    protected $company;

    protected $lastPrRow;

    protected $lastPoRow;

    protected $lastApInvoiceRow;

    protected $lastTrxRow;

    protected $lastPurchasing;

    /**
     *
     * @return boolean
     */
    public function isValid()
    {

        /**
         *
         * @var Notification $notification
         */
        $notification = $this->validate();
        return ! $notification->hasErrors();
    }

    abstract public function validate();
    
    
    

    /**
     *
     * @param ItemSnapshot $itemSnapshot
     */
    public function makeItemFrom($itemSnapshot)
    {
        if (! $itemSnapshot instanceof ItemSnapshot)
            return;

        $this->id = $itemSnapshot->id;
        $this->warehouseId = $itemSnapshot->warehouseId;
        $this->itemSku = $itemSnapshot->itemSku;
        $this->itemName = $itemSnapshot->itemName;
        $this->itemNameForeign = $itemSnapshot->itemNameForeign;
        $this->itemDescription = $itemSnapshot->itemDescription;
        $this->itemType = $itemSnapshot->itemType;
        $this->itemCategory = $itemSnapshot->itemCategory;
        $this->keywords = $itemSnapshot->keywords;
        $this->isActive = $itemSnapshot->isActive;
        $this->isStocked = $itemSnapshot->isStocked;
        $this->isSaleItem = $itemSnapshot->isSaleItem;
        $this->isPurchased = $itemSnapshot->isPurchased;
        $this->isFixedAsset = $itemSnapshot->isFixedAsset;
        $this->isSparepart = $itemSnapshot->isSparepart;
        $this->uom = $itemSnapshot->uom;
        $this->barcode = $itemSnapshot->barcode;
        $this->barcode39 = $itemSnapshot->barcode39;
        $this->barcode128 = $itemSnapshot->barcode128;
        $this->status = $itemSnapshot->status;
        $this->createdOn = $itemSnapshot->createdOn;
        $this->manufacturer = $itemSnapshot->manufacturer;
        $this->manufacturerCode = $itemSnapshot->manufacturerCode;
        $this->manufacturerCatalog = $itemSnapshot->manufacturerCatalog;
        $this->manufacturerModel = $itemSnapshot->manufacturerModel;
        $this->manufacturerSerial = $itemSnapshot->manufacturerSerial;
        $this->origin = $itemSnapshot->origin;
        $this->serialNumber = $itemSnapshot->serialNumber;
        $this->lastPurchasePrice = $itemSnapshot->lastPurchasePrice;
        $this->lastPurchaseCurrency = $itemSnapshot->lastPurchaseCurrency;
        $this->lastPurchaseDate = $itemSnapshot->lastPurchaseDate;
        $this->leadTime = $itemSnapshot->leadTime;
        $this->validFromDate = $itemSnapshot->validFromDate;
        $this->validToDate = $itemSnapshot->validToDate;
        $this->location = $itemSnapshot->location;
        $this->itemInternalLabel = $itemSnapshot->itemInternalLabel;
        $this->assetLabel = $itemSnapshot->assetLabel;
        $this->sparepartLabel = $itemSnapshot->sparepartLabel;
        $this->remarks = $itemSnapshot->remarks;
        $this->localAvailabiliy = $itemSnapshot->localAvailabiliy;
        $this->lastChangeOn = $itemSnapshot->lastChangeOn;
        $this->token = $itemSnapshot->token;
        $this->checksum = $itemSnapshot->checksum;
        $this->currentState = $itemSnapshot->currentState;
        $this->docNumber = $itemSnapshot->docNumber;
        $this->monitoredBy = $itemSnapshot->monitoredBy;
        $this->sysNumber = $itemSnapshot->sysNumber;
        $this->remarksText = $itemSnapshot->remarksText;
        $this->revisionNo = $itemSnapshot->revisionNo;
        $this->itemSku1 = $itemSnapshot->itemSku1;
        $this->itemSku2 = $itemSnapshot->itemSku2;
        $this->assetGroup = $itemSnapshot->assetGroup;
        $this->assetClass = $itemSnapshot->assetClass;
        $this->stockUomConvertFactor = $itemSnapshot->stockUomConvertFactor;
        $this->purchaseUomConvertFactor = $itemSnapshot->purchaseUomConvertFactor;
        $this->salesUomConvertFactor = $itemSnapshot->salesUomConvertFactor;
        $this->capacity = $itemSnapshot->capacity;
        $this->avgUnitPrice = $itemSnapshot->avgUnitPrice;
        $this->standardPrice = $itemSnapshot->standardPrice;
        $this->uuid = $itemSnapshot->uuid;
        $this->createdBy = $itemSnapshot->createdBy;
        $this->itemGroup = $itemSnapshot->itemGroup;
        $this->stockUom = $itemSnapshot->stockUom;
        $this->cogsAccount = $itemSnapshot->cogsAccount;
        $this->purchaseUom = $itemSnapshot->purchaseUom;
        $this->salesUom = $itemSnapshot->salesUom;
        $this->inventoryAccount = $itemSnapshot->inventoryAccount;
        $this->expenseAccount = $itemSnapshot->expenseAccount;
        $this->revenueAccount = $itemSnapshot->revenueAccount;
        $this->defaultWarehouse = $itemSnapshot->defaultWarehouse;
        $this->lastChangeBy = $itemSnapshot->lastChangeBy;
        $this->standardUom = $itemSnapshot->standardUom;
        $this->company = $itemSnapshot->company;
        $this->lastPrRow = $itemSnapshot->lastPrRow;
        $this->lastPoRow = $itemSnapshot->lastPoRow;
        $this->lastApInvoiceRow = $itemSnapshot->lastApInvoiceRow;
        $this->lastTrxRow = $itemSnapshot->lastTrxRow;
        $this->lastPurchasing = $itemSnapshot->lastPurchasing;
        $this->itemTypeId = $itemSnapshot->itemTypeId;
    }

    /**
     *
     * @return ItemDTO;
     */
    public function createItemDTO()
    {
        $itemDTO = ItemAssembler::createItemDTO($this);
        return $itemDTO;
    }

    /**
     *
     * @return ItemSnapshot;
     */
    public function createItemSnapshot()
    {
        $itemSnapshot = ItemSnapshotAssembler::createItemSnapshotFrom($this);
        return $itemSnapshot;
    }

    /**
     *
     * @return string
     */
    public function getItemType()
    {
        return ItemType::UNKNOWN_ITEM_TYPE;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    public function getItemSku()
    {
        return $this->itemSku;
    }

    public function getItemName()
    {
        return $this->itemName;
    }

    public function getItemNameForeign()
    {
        return $this->itemNameForeign;
    }

    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getIsStocked()
    {
        return $this->isStocked;
    }

    public function getIsSaleItem()
    {
        return $this->isSaleItem;
    }

    public function getIsPurchased()
    {
        return $this->isPurchased;
    }

    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    public function getIsSparepart()
    {
        return $this->isSparepart;
    }

    public function getUom()
    {
        return $this->uom;
    }

    public function getBarcode()
    {
        return $this->barcode;
    }

    public function getBarcode39()
    {
        return $this->barcode39;
    }

    public function getBarcode128()
    {
        return $this->barcode128;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    public function getManufacturerCode()
    {
        return $this->manufacturerCode;
    }

    public function getManufacturerCatalog()
    {
        return $this->manufacturerCatalog;
    }

    public function getManufacturerModel()
    {
        return $this->manufacturerModel;
    }

    public function getManufacturerSerial()
    {
        return $this->manufacturerSerial;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    public function getLastPurchasePrice()
    {
        return $this->lastPurchasePrice;
    }

    public function getLastPurchaseCurrency()
    {
        return $this->lastPurchaseCurrency;
    }

    public function getLastPurchaseDate()
    {
        return $this->lastPurchaseDate;
    }

    public function getLeadTime()
    {
        return $this->leadTime;
    }

    public function getValidFromDate()
    {
        return $this->validFromDate;
    }

    public function getValidToDate()
    {
        return $this->validToDate;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getItemInternalLabel()
    {
        return $this->itemInternalLabel;
    }

    public function getAssetLabel()
    {
        return $this->assetLabel;
    }

    public function getSparepartLabel()
    {
        return $this->sparepartLabel;
    }

    public function getRemarks()
    {
        return $this->remarks;
    }

    public function getLocalAvailabiliy()
    {
        return $this->localAvailabiliy;
    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getChecksum()
    {
        return $this->checksum;
    }

    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function getDocNumber()
    {
        return $this->docNumber;
    }

    public function getMonitoredBy()
    {
        return $this->monitoredBy;
    }

    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    public function getRemarksText()
    {
        return $this->remarksText;
    }

    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    public function getItemSku1()
    {
        return $this->itemSku1;
    }

    public function getItemSku2()
    {
        return $this->itemSku2;
    }

    public function getAssetGroup()
    {
        return $this->assetGroup;
    }

    public function getAssetClass()
    {
        return $this->assetClass;
    }

    public function getStockUomConvertFactor()
    {
        return $this->stockUomConvertFactor;
    }

    public function getPurchaseUomConvertFactor()
    {
        return $this->purchaseUomConvertFactor;
    }

    public function getSalesUomConvertFactor()
    {
        return $this->salesUomConvertFactor;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function getAvgUnitPrice()
    {
        return $this->avgUnitPrice;
    }

    public function getStandardPrice()
    {
        return $this->standardPrice;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getItemTypeId()
    {
        return $this->itemTypeId;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    public function getStockUom()
    {
        return $this->stockUom;
    }

    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    public function getPurchaseUom()
    {
        return $this->purchaseUom;
    }

    public function getSalesUom()
    {
        return $this->salesUom;
    }

    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }

    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }

    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    public function getStandardUom()
    {
        return $this->standardUom;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getLastPrRow()
    {
        return $this->lastPrRow;
    }

    public function getLastPoRow()
    {
        return $this->lastPoRow;
    }

    public function getLastApInvoiceRow()
    {
        return $this->lastApInvoiceRow;
    }

    public function getLastTrxRow()
    {
        return $this->lastTrxRow;
    }

    public function getLastPurchasing()
    {
        return $this->lastPurchasing;
    }
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }

       
}