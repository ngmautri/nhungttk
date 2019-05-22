<?php
namespace Inventory\Domain\Item;

use Inventory\Application\DTO\ItemAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractItem
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
     * @return string
     */
    public function getItemType()
    {
        return ItemType::UNKNOWN_ITEM_TYPE;
    }
}