<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryItem;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemMapper
{

    public static function mapSnapshotEntity(EntityManager $doctrineEM, ItemSnapshot $snapshot, NmtInventoryItem $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setWarehouseId($snapshot->warehouseId);
        $entity->setItemSku($snapshot->itemSku);
        $entity->setItemName($snapshot->itemName);
        $entity->setItemNameForeign($snapshot->itemNameForeign);
        $entity->setItemDescription($snapshot->itemDescription);
        $entity->setItemType($snapshot->itemType);
        $entity->setItemCategory($snapshot->itemCategory);
        $entity->setKeywords($snapshot->keywords);
        $entity->setIsActive($snapshot->isActive);
        $entity->setIsStocked($snapshot->isStocked);
        $entity->setIsSaleItem($snapshot->isSaleItem);
        $entity->setIsPurchased($snapshot->isPurchased);
        $entity->setIsFixedAsset($snapshot->isFixedAsset);
        $entity->setIsSparepart($snapshot->isSparepart);
        $entity->setUom($snapshot->uom);
        $entity->setBarcode($snapshot->barcode);
        $entity->setBarcode39($snapshot->barcode39);
        $entity->setBarcode128($snapshot->barcode128);
        $entity->setStatus($snapshot->status);
        $entity->setManufacturer($snapshot->manufacturer);
        $entity->setManufacturerCode($snapshot->manufacturerCode);
        $entity->setManufacturerCatalog($snapshot->manufacturerCatalog);
        $entity->setManufacturerModel($snapshot->manufacturerModel);
        $entity->setManufacturerSerial($snapshot->manufacturerSerial);
        $entity->setOrigin($snapshot->origin);
        $entity->setSerialNumber($snapshot->serialNumber);
        $entity->setLastPurchasePrice($snapshot->lastPurchasePrice);
        $entity->setLastPurchaseCurrency($snapshot->lastPurchaseCurrency);
        $entity->setLeadTime($snapshot->leadTime);
        $entity->setLocation($snapshot->location);
        $entity->setItemInternalLabel($snapshot->itemInternalLabel);
        $entity->setAssetLabel($snapshot->assetLabel);
        $entity->setSparepartLabel($snapshot->sparepartLabel);
        $entity->setRemarks($snapshot->remarks);
        $entity->setLocalAvailabiliy($snapshot->localAvailabiliy);
        $entity->setToken($snapshot->token);
        $entity->setChecksum($snapshot->checksum);
        $entity->setCurrentState($snapshot->currentState);
        $entity->setDocNumber($snapshot->docNumber);
        $entity->setMonitoredBy($snapshot->monitoredBy);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setRemarksText($snapshot->remarksText);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setItemSku1($snapshot->itemSku1);
        $entity->setItemSku2($snapshot->itemSku2);
        $entity->setAssetGroup($snapshot->assetGroup);
        $entity->setAssetClass($snapshot->assetClass);
        $entity->setStockUomConvertFactor($snapshot->stockUomConvertFactor);
        $entity->setPurchaseUomConvertFactor($snapshot->purchaseUomConvertFactor);
        $entity->setSalesUomConvertFactor($snapshot->salesUomConvertFactor);
        $entity->setCapacity($snapshot->capacity);
        $entity->setAvgUnitPrice($snapshot->avgUnitPrice);
        $entity->setStandardPrice($snapshot->standardPrice);
        $entity->setUuid($snapshot->uuid);
        $entity->setItemTypeId($snapshot->itemTypeId);

        // ============================
        // DATE MAPPING
        // ============================

        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastPurchaseDate($snapshot->lastPurchaseDate);
         * $entity->setValidFromDate($snapshot->validFromDate);
         * $entity->setValidToDate($snapshot->validToDate);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         */

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastPurchaseDate !== null) {
            $entity->setLastPurchaseDate(new \DateTime($snapshot->lastPurchaseDate));
        }

        if ($snapshot->validFromDate !== null) {
            $entity->setValidFromDate(new \DateTime($snapshot->validFromDate));
        }

        if ($snapshot->validToDate !== null) {
            $entity->setValidToDate(new \DateTime($snapshot->validToDate));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        // $entity->setCreatedBy($snapshot->createdBy);
        // $entity->setItemGroup($snapshot->itemGroup);
        // $entity->setStockUom($snapshot->stockUom);
        // $entity->setCogsAccount($snapshot->cogsAccount);
        // $entity->setPurchaseUom($snapshot->purchaseUom);
        // $entity->setSalesUom($snapshot->salesUom);
        // $entity->setInventoryAccount($snapshot->inventoryAccount);
        // $entity->setExpenseAccount($snapshot->expenseAccount);
        // $entity->setRevenueAccount($snapshot->revenueAccount);
        // $entity->setDefaultWarehouse($snapshot->defaultWarehouse);
        // $entity->setLastChangeBy($snapshot->lastChangeBy);
        // $entity->setStandardUom($snapshot->standardUom);
        // $entity->setCompany($snapshot->company);
        // $entity->setLastPrRow($snapshot->lastPrRow);
        // $entity->setLastPoRow($snapshot->lastPoRow);
        // $entity->setLastApInvoiceRow($snapshot->lastApInvoiceRow);
        // $entity->setLastTrxRow($snapshot->lastTrxRow);
        // $entity->setLastPurchasing($snapshot->lastPurchasing);

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->itemGroup > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItemGroup $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->find($snapshot->itemGroup);
            $entity->setItemGroup($obj);
        }

        if ($snapshot->stockUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->stockUom);
            $entity->setStockUom($obj);
        }

        if ($snapshot->cogsAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->cogsAccount);
            $entity->setCogsAccount($obj);
        }

        if ($snapshot->purchaseUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->purchaseUom);
            $entity->setPurchaseUom($obj);
        }

        if ($snapshot->salesUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->salesUom);
            $entity->setSalesUom($obj);
        }

        if ($snapshot->inventoryAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->inventoryAccount);
            $entity->setInventoryAccount($obj);
        }

        if ($snapshot->expenseAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->expenseAccount);
            $entity->setExpenseAccount($obj);
        }

        if ($snapshot->revenueAccount > 0) {
            /**
             *
             * @var \Application\Entity\FinAccount $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinAccount')->find($snapshot->revenueAccount);
            $entity->setRevenueAccount($obj);
        }

        if ($snapshot->defaultWarehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->defaultWarehouse);
            $entity->setDefaultWarehouse($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }

        if ($snapshot->standardUom > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationUom $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->find($snapshot->standardUom);
            $entity->setStandardUom($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->lastPrRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->lastPrRow);
            $entity->setLastPrRow($obj);
        }

        if ($snapshot->lastPoRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->lastPoRow);
            $entity->setLastPoRow($obj);
        }

        if ($snapshot->lastApInvoiceRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->lastApInvoiceRow);
            $entity->setLastApInvoiceRow($obj);
        }

        if ($snapshot->lastTrxRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryTrx $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->find($snapshot->lastTrxRow);
            $entity->setLastTrxRow($obj);
        }

        return $entity;
    }

    /**
     *
     * @param NmtInventoryItem $entity
     * @param ItemSnapshot $snapshot
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshot(NmtInventoryItem $entity, ItemSnapshot $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new ItemSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================
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
        $snapshot->lastPurchaseDate = $entity->getLastPurchaseDate();
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

        // modification
        $snapshot->assetLabel1 = preg_replace('/[0-]/', '', \substr($snapshot->assetLabel, - 5));

        // ============================
        // DATE MAPPING
        // ============================

        // $snapshot->createdOn = $entity->getCreatedOn();
        // $snapshot->lastChangeOn = $entity->getLastChangeOn();
        // $snapshot->validFromDate = $entity->getValidFromDate();
        // $snapshot->validToDate = $entity->getValidToDate();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getValidFromDate() == null) {
            $snapshot->validFromDate = $entity->getValidFromDate()->format("Y-m-d");
        }

        if (! $entity->getValidToDate() == null) {
            $snapshot->validToDate = $entity->getValidToDate()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->itemGroup = $entity->getItemGroup();
         * $snapshot->stockUom = $entity->getStockUom();
         * $snapshot->cogsAccount = $entity->getCogsAccount();
         * $snapshot->purchaseUom = $entity->getPurchaseUom();
         * $snapshot->salesUom = $entity->getSalesUom();
         * $snapshot->inventoryAccount = $entity->getInventoryAccount();
         * $snapshot->expenseAccount = $entity->getExpenseAccount();
         * $snapshot->revenueAccount = $entity->getRevenueAccount();
         * $snapshot->defaultWarehouse = $entity->getDefaultWarehouse();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->standardUom = $entity->getStandardUom();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->lastPrRow = $entity->getLastPrRow();
         * $snapshot->lastPoRow = $entity->getLastPoRow();
         * $snapshot->lastApInvoiceRow = $entity->getLastApInvoiceRow();
         * $snapshot->lastTrxRow = $entity->getLastTrxRow();
         * $snapshot->lastPurchasing = $entity->getLastPurchasing();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getItemGroup() !== null) {
            $snapshot->itemGroup = $entity->getItemGroup()->getId();
        }

        if ($entity->getStockUom() !== null) {
            $snapshot->stockUom = $entity->getStockUom()->getId();
        }

        if ($entity->getCogsAccount() !== null) {
            $snapshot->cogsAccount = $entity->getCogsAccount()->getId();
        }

        if ($entity->getPurchaseUom() !== null) {
            $snapshot->purchaseUom = $entity->getPurchaseUom()->getId();
        }

        if ($entity->getSalesUom() !== null) {
            $snapshot->salesUom = $entity->getSalesUom()->getId();
        }

        if ($entity->getInventoryAccount() !== null) {
            $snapshot->inventoryAccount = $entity->getInventoryAccount()->getId();
        }

        if ($entity->getExpenseAccount() !== null) {
            $snapshot->expenseAccount = $entity->getExpenseAccount()->getId();
        }

        if ($entity->getRevenueAccount() !== null) {
            $snapshot->getRevenueAccount = $entity->getRevenueAccount()->getId();
        }

        if ($entity->getDefaultWarehouse() !== null) {
            $snapshot->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getStandardUom() !== null) {
            $snapshot->standardUom = $entity->getStandardUom()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getLastPrRow() !== null) {
            $snapshot->lastPrRow = $entity->getLastPrRow()->getId();
        }

        if ($entity->getLastPoRow() !== null) {
            $snapshot->lastPoRow = $entity->getLastPoRow()->getId();
        }

        if ($entity->getLastApInvoiceRow() !== null) {
            $snapshot->lastApInvoiceRow = $entity->getLastApInvoiceRow()->getId();
        }

        if ($entity->getLastTrxRow() !== null) {
            $snapshot->lastTrxRow = $entity->getLastTrxRow()->getId();
        }

        if ($entity->getLastPurchasing() !== null) {
            $snapshot->lastPurchasing = $entity->getLastPurchasing()->getId();
        }

        return $snapshot;
    }
}
