<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\NmtApplicationUom;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryWarehouse;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePrRow;
use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowMapper
{

    /**
     *
     * @param \Procure\Domain\RowSnapshot $snapshot
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updateWarehouseDetails(RowSnapshot $snapshot, NmtInventoryWarehouse $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->warehouse = $entity->getId();
        $snapshot->warehouseCode = $entity->getWhCode();
        $snapshot->warehouseName = $entity->getWhName();
        $snapshot->docWarehouseCode = $snapshot->warehouseCode;
        $snapshot->docWarehouseName = $snapshot->warehouseName;
        return $snapshot;
    }
    
    /**
     * 
     * @param \Procure\Domain\RowSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationUom $entity
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updateUomDetails(RowSnapshot $snapshot, NmtApplicationUom $entity)
    {
        if ($snapshot == null) {
            return null;
        }
        
        if ($entity == null) {
            return $snapshot;
        }
        
        $snapshot->docUom = $entity->getId();
        $snapshot->docUomCode = $entity->getUomCode();
        $snapshot->docUomName = $entity->getUomName();
        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param NmtInventoryItem $entity
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updateItemDetails(RowSnapshot $snapshot, NmtInventoryItem $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->item = $entity->getId();
        $snapshot->itemToken = $entity->getToken();
        $snapshot->itemName = $entity->getItemName();
        $snapshot->itemName1 = $entity->getItemNameForeign();
        $snapshot->itemSKU = $entity->getItemSku();
        $snapshot->itemSKU1 = $entity->getItemSku1();
        $snapshot->itemSKU2 = $entity->getItemSku2();
        $snapshot->itemChecksum = $entity->getChecksum();
        $snapshot->itemVersion = $entity->getRevisionNo();

        if ($entity->getStandardUom() != null) {
            $snapshot->itemStandardUnit = $entity->getStandardUom()->getId();
            $snapshot->itemStandardUnitName = $entity->getStandardUom()->getUomName();
            $snapshot->itemStandardUnitCode = $entity->getStandardUom()->getUomCode();
        }

        if ($entity->getIsFixedAsset() == 1) {
            $snapshot->isFixedAsset = 1;
        }

        if ($entity->getIsStocked() == 1) {
            $snapshot->isInventoryItem = 1;
        }

        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param NmtProcurePrRow $entity
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updatePRDetails(RowSnapshot $snapshot, NmtProcurePrRow $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->prRow = $entity->getId();
        $snapshot->prRowIndentifer = $entity->getRowIdentifer();
        $snapshot->prRowCode = $entity->getRowCode();
        $snapshot->prRowName = $entity->getRowName();
        $snapshot->prRowConvertFactor = $entity->getConversionFactor();
        $snapshot->prRowUnit = $entity->getRowUnit();

        if ($entity->getPr() !== null) {
            $snapshot->pr = $entity->getPr()->getId();
            $snapshot->prSysNumber = $entity->getPr()->getPrAutoNumber();
            $snapshot->prNumber = $entity->getPr()->getPrNumber();
            $snapshot->prToken = $entity->getPr()->getToken();
            $snapshot->prChecksum = $entity->getPr()->getChecksum();
        }

        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param NmtProcurePo $entity
     */
    public static function updatePODetails(RowSnapshot $snapshot, NmtProcurePo $entity)
    {
        $snapshot->po = $entity->getId();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->docCurrencyISO = $entity->getCurrencyIso3();

        $snapshot->docId = $snapshot->po;
        $snapshot->docToken = $entity->getToken();

        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->docNumber = $entity->getContractNo();

        if ($entity->getDocCurrency() !== null) {
            $snapshot->docCurrencyId = $entity->getDocCurrency()->getId();
            $snapshot->docCurrencyISO = $entity->getDocCurrency()->getCurrency();
        }

        if ($entity->getLocalCurrency() !== null) {
            $snapshot->localCurrencyId = $entity->getLocalCurrency()->getId();
            $snapshot->localCurrencyISO = $entity->getLocalCurrency()->getCurrency();
        }

        if ($entity->getVendor() !== null) {
            $snapshot->vendorId = $entity->getVendor()->getId();
            $snapshot->vendorToken = $entity->getVendor()->getToken();

            if ($entity->getVendor()->getCountry() !== null) {
                $snapshot->vendorCountry = $entity->getVendor()
                    ->getCountry()
                    ->getCountryName();
            }
        }

        return $snapshot;
    }
}
