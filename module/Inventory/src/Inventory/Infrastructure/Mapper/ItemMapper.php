<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryItem;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemMapper
{

    /**
     *
     * @param NmtInventoryItem $entity
     * @param ItemSnapshot $snapshot
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshot(NmtInventoryItem $entity, ItemSnapshot $snapshot)
    {
        if ($entity == null)
            return null;

        if ($snapshot == null)
            return null;

        // Mapping Reference
        // =====================
        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->itemGroup = $entity->getItemGroup();
        if ($entity->getItemGroup() !== null) {
            $snapshot->itemGroup = $entity->getItemGroup()->getId();
        }

        $snapshot->stockUom = $entity->getItemGroup();
        if ($entity->getItemGroup() !== null) {
            $snapshot->stockUom = $entity->getItemGroup()->getId();
        }

        $snapshot->cogsAccount = $entity->getCogsAccount();
        if ($entity->getCogsAccount() !== null) {
            $snapshot->cogsAccount = $entity->getCogsAccount()->getId();
        }

        $snapshot->purchaseUom = $entity->getPurchaseUom();
        if ($entity->getPurchaseUom() !== null) {
            $snapshot->purchaseUom = $entity->getPurchaseUom()->getId();
        }

        $snapshot->salesUom = $entity->getSalesUom();
        if ($entity->getSalesUom() !== null) {
            $snapshot->salesUom = $entity->getSalesUom()->getId();
        }

        $snapshot->inventoryAccount = $entity->getInventoryAccount();
        if ($entity->getInventoryAccount() !== null) {
            $snapshot->inventoryAccount = $entity->getInventoryAccount()->getId();
        }

        $snapshot->expenseAccount = $entity->getExpenseAccount();
        if ($entity->getExpenseAccount() !== null) {
            $snapshot->expenseAccount = $entity->getExpenseAccount()->getId();
        }

        $snapshot->revenueAccount = $entity->getRevenueAccount();
        if ($entity->getRevenueAccount() !== null) {
            $snapshot->getRevenueAccount = $entity->getRevenueAccount()->getId();
        }

        $snapshot->defaultWarehouse = $entity->getDefaultWarehouse();
        if ($entity->getDefaultWarehouse() !== null) {
            $snapshot->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
        }

        $snapshot->lastChangeBy = $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        $snapshot->standardUom = $entity->getStandardUom();
        if ($entity->getStandardUom() !== null) {
            $snapshot->standardUom = $entity->getStandardUom()->getId();
        }

        $snapshot->company = $entity->getCompany();
        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        $snapshot->lastPrRow = $entity->getLastPrRow();
        if ($entity->getLastPrRow() !== null) {
            $snapshot->lastPrRow = $entity->getLastPrRow()->getId();
        }

        $snapshot->lastPoRow = $entity->getLastPoRow();
        if ($entity->getLastPoRow() !== null) {
            $snapshot->lastPoRow = $entity->getLastPoRow()->getId();
        }

        $snapshot->lastApInvoiceRow = $entity->getLastApInvoiceRow();
        if ($entity->getLastApInvoiceRow() !== null) {
            $snapshot->lastApInvoiceRow = $entity->getLastApInvoiceRow()->getId();
        }

        $snapshot->lastTrxRow = $entity->getLastTrxRow();
        if ($entity->getLastTrxRow() !== null) {
            $snapshot->invoice = $entity->getLastTrxRow()->getId();
        }

        $snapshot->lastPurchasing = $entity->getLastPurchasing();
        if ($entity->getLastPurchasing() !== null) {
            $snapshot->lastPurchasing = $entity->getLastPurchasing()->getId();
        }

        // Mapping Date
        // =====================
        $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        $snapshot->validFromDate = $entity->getValidFromDate();
        if (! $entity->getValidFromDate() == null) {
            $snapshot->validFromDate = $entity->getValidFromDate()->format("Y-m-d");
        }

        $snapshot->validToDate = $entity->getValidToDate();
        if (! $entity->getValidToDate() == null) {
            $snapshot->validToDate = $entity->getValidToDate()->format("Y-m-d");
        }

        $snapshot->lastPurchaseDate = $entity->getLastPurchaseDate();
        if (! $entity->getLastPurchaseDate() == null) {
            $snapshot->lastPurchaseDate = $entity->getLastPurchaseDate()->format("Y-m-d");
        }

        $snapshot->lastChangeOn = $entity->getLastChangeOn();

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // Mapping None-Object Field
        // =====================

        $snapshot->id = $entity->getId();
        $snapshot->warehouseId = $entity->getWarehouseId();
        $snapshot->itemSku = $entity->getItemSku();
        $snapshot->itemName = $entity->getItemName();
        $snapshot->itemNameForeign = $entity->getItemNameForeign();
        $snapshot->itemDescription = $entity->getItemDescription();
        $snapshot->itemType = $entity->getItemType();
        $snapshot->itemCategory = $entity->getItemCategory();
        $snapshot->keywords = $entity->getKeywords();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->isStocked = $entity->getIsStocked();
        $snapshot->isSaleItem = $entity->getIsSaleItem();
        $snapshot->isPurchased = $entity->getIsPurchased();
        $snapshot->isFixedAsset = $entity->getIsFixedAsset();
        $snapshot->isSparepart = $entity->getIsSparepart();
        $snapshot->uom = $entity->getUom();
        $snapshot->barcode = $entity->getBarcode();
        $snapshot->barcode39 = $entity->getBarcode39();
        $snapshot->barcode128 = $entity->getBarcode128();
        $snapshot->status = $entity->getStatus();
        $snapshot->manufacturer = $entity->getManufacturer();
        $snapshot->manufacturerCode = $entity->getManufacturerCode();
        $snapshot->manufacturerCatalog = $entity->getManufacturerCatalog();
        $snapshot->manufacturerModel = $entity->getManufacturerModel();
        $snapshot->manufacturerSerial = $entity->getManufacturerSerial();
        $snapshot->origin = $entity->getOrigin();
        $snapshot->serialNumber = $entity->getSerialNumber();
        $snapshot->lastPurchasePrice = $entity->getLastPurchasePrice();
        $snapshot->lastPurchaseCurrency = $entity->getLastPurchaseCurrency();
        $snapshot->leadTime = $entity->getLeadTime();
        $snapshot->location = $entity->getLocation();
        $snapshot->itemInternalLabel = $entity->getItemInternalLabel();
        $snapshot->assetLabel = $entity->getAssetLabel();
        $snapshot->sparepartLabel = $entity->getSparepartLabel();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->localAvailabiliy = $entity->getLocalAvailabiliy();
        $snapshot->token = $entity->getToken();
        $snapshot->checksum = $entity->getChecksum();
        $snapshot->currentState = $entity->getCurrentState();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->monitoredBy = $entity->getMonitoredBy();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->remarksText = $entity->getRemarksText();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->itemSku1 = $entity->getItemSku1();
        $snapshot->itemSku2 = $entity->getItemSku2();
        $snapshot->assetGroup = $entity->getAssetGroup();
        $snapshot->assetClass = $entity->getAssetClass();
        $snapshot->stockUomConvertFactor = $entity->getStockUomConvertFactor();
        $snapshot->purchaseUomConvertFactor = $entity->getPurchaseUomConvertFactor();
        $snapshot->salesUomConvertFactor = $entity->getSalesUomConvertFactor();
        $snapshot->capacity = $entity->getCapacity();
        $snapshot->avgUnitPrice = $entity->getAvgUnitPrice();
        $snapshot->standardPrice = $entity->getStandardPrice();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->itemTypeId = $entity->getItemTypeId();

        return $snapshot;
    }
}
