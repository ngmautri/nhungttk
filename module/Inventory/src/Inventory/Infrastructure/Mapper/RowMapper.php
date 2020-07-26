<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\FinAccount;
use Application\Entity\FinCostCenter;
use Application\Entity\FinVendorInvoice;
use Application\Entity\NmtApplicationUom;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryWarehouse;
use Application\Entity\NmtProcureGr;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Application\Entity\NmtProcureQo;
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
    public static function updateGLAccountDetails(RowSnapshot $snapshot, FinAccount $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }
        $snapshot->glAccount = $entity->getId();
        $snapshot->glAccountName = $entity->getAccountName();
        $snapshot->glAccountNumber = $entity->getAccountNumber();
        $snapshot->glAccountType = $entity->getAccountType();
        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\RowSnapshot $snapshot
     * @param \Application\Entity\FinCostCenter $entity
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updateCostCenterDetails(RowSnapshot $snapshot, FinCostCenter $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->costCenter = $entity->getId();
        $snapshot->costCenterName = $entity->getCostCenterName();
        return $snapshot;
    }

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
        $snapshot->itemMonitorMethod = $entity->getMonitoredBy();
        $snapshot->itemAssetLabel = $entity->getAssetLabel();

        $snapshot->itemAssetLabel1 = preg_replace('/[-]/', '', \substr($snapshot->itemAssetLabel, - 5));

        $snapshot->itemManufacturerCode = $entity->getManufacturerCode();
        $snapshot->itemManufacturerModel = $entity->getManufacturerModel();
        $snapshot->itemManufacturerSerial = $entity->getManufacturerSerial();
        $snapshot->itemDefaultManufacturer = $entity->getManufacturer();
        $snapshot->itemKeywords = $entity->getKeywords();
        $snapshot->itemSysNumber = $entity->getSysNumber();
        $snapshot->itemDescription = $entity->getItemDescription();

        if ($entity->getItemGroup() != null) {

            if ($entity->getItemGroup()->getInventoryAccount() != null) {
                $snapshot->itemInventoryGL = $entity->getItemGroup()
                    ->getInventoryAccount()
                    ->getId();
            }

            if ($entity->getItemGroup()->getCogsAccount() != null) {
                $snapshot->itemCogsGL = $entity->getItemGroup()
                    ->getCogsAccount()
                    ->getId();
            }

            if ($entity->getItemGroup()->getCostCenter() != null) {
                $snapshot->itemCostCenter = $entity->getItemGroup()
                    ->getCostCenter()
                    ->getId();
            }
        }

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

            if ($entity->getPr()->getDepartment() !== null) {
                $snapshot->prDepartment = $entity->getPr()
                    ->getDepartment()
                    ->getNodeId();
                $snapshot->prDepartmentName = $entity->getPr()
                    ->getDepartment()
                    ->getNodeName();
            }
        }

        return $snapshot;
    }

    public static function updatePRHeaderDetails(RowSnapshot $snapshot, NmtProcurePr $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->pr = $entity->getId();
        $snapshot->prSysNumber = $entity->getPrAutoNumber();
        $snapshot->docSysNumber = $entity->getPrAutoNumber();
        $snapshot->prNumber = $entity->getPrNumber();
        $snapshot->prToken = $entity->getToken();
        $snapshot->prChecksum = $entity->getChecksum();
        $snapshot->docId = $snapshot->pr;
        $snapshot->docToken = $entity->getToken();
        $snapshot->docNumber = $entity->getDocNumber();

        if (! $entity->getSubmittedOn() == null) {
            $snapshot->docDate = $entity->getSubmittedOn()->format("Y-m-d");
            $snapshot->docYear = $entity->getSubmittedOn()->format("Y");
            $snapshot->docMonth = $entity->getSubmittedOn()->format("m");
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
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        if ($entity->getCompany()) {
            $snapshot->companyId = $entity->getCompany()->getId();
            $snapshot->companyToken = $entity->getCompany()->getToken();
            $snapshot->companyName = $entity->getCompany()->getCompanyName();
        }

        $snapshot->po = $entity->getId();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->docCurrencyISO = $entity->getCurrencyIso3();

        $snapshot->docId = $snapshot->po;
        $snapshot->docToken = $entity->getToken();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->docRevisionNo = $entity->getRevisionNo();

        $snapshot->exchangeRate = $entity->getExchangeRate();

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

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
            $snapshot->docYear = $entity->getDocDate()->format("Y");
            $snapshot->docMonth = $entity->getDocDate()->format("m");
        }

        return $snapshot;
    }

    public static function updateInvoiceDetails(RowSnapshot $snapshot, FinVendorInvoice $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        if ($entity->getCompany()) {
            $snapshot->companyId = $entity->getCompany()->getId();
            $snapshot->companyToken = $entity->getCompany()->getToken();
            $snapshot->companyName = $entity->getCompany()->getCompanyName();
        }

        $snapshot->invoice = $entity->getId();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->docCurrencyISO = $entity->getCurrencyIso3();

        $snapshot->docId = $snapshot->invoice;
        $snapshot->docToken = $entity->getToken();

        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->docNumber = $entity->getDocNumber();

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
            $snapshot->vendorName = $entity->getVendor()->getVendorName();

            if ($entity->getVendor()->getCountry() !== null) {
                $snapshot->vendorCountry = $entity->getVendor()
                    ->getCountry()
                    ->getCountryName();
            }
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
            $snapshot->docYear = $entity->getDocDate()->format("Y");
            $snapshot->docMonth = $entity->getDocDate()->format("m");
        }

        return $snapshot;
    }

    public static function updateGRDetails(RowSnapshot $snapshot, NmtProcureGr $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        if ($entity->getCompany()) {
            $snapshot->companyId = $entity->getCompany()->getId();
            $snapshot->companyToken = $entity->getCompany()->getToken();
            $snapshot->companyName = $entity->getCompany()->getCompanyName();
        }

        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->docCurrencyISO = $entity->getCurrencyIso3();

        $snapshot->docId = $entity->getId();
        $snapshot->docToken = $entity->getToken();
        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->docNumber = $entity->getDocNumber();

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

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
            $snapshot->docYear = $entity->getDocDate()->format("Y");
            $snapshot->docMonth = $entity->getDocDate()->format("m");
        }

        return $snapshot;
    }

    public static function updateQuoteDetails(RowSnapshot $snapshot, NmtProcureQo $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        // $snapshot->qo = $entity->getId();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->docCurrencyISO = $entity->getCurrencyIso3();

        $snapshot->docId = $entity->getId();
        $snapshot->docToken = $entity->getToken();

        $snapshot->exchangeRate = $entity->getExchangeRate();
        $snapshot->docNumber = $entity->getDocNumber();

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

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d");
            $snapshot->docYear = $entity->getDocDate()->format("Y");
            $snapshot->docMonth = $entity->getDocDate()->format("m");
        }

        return $snapshot;
    }
}
