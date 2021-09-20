<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationCurrency;
use Application\Entity\NmtApplicationIncoterms;
use Application\Entity\NmtApplicationPmtMethod;
use Application\Entity\NmtApplicationPmtTerm;
use Application\Entity\NmtBpVendor;
use Application\Entity\NmtFinPostingPeriod;
use Application\Entity\NmtInventoryWarehouse;
use Procure\Domain\DocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeaderMapper
{

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationIncoterms $entity
     */
    public static function updateIncotermDetails(DocSnapshot $snapshot, NmtApplicationIncoterms $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->incoterm = $entity->getId();
        $snapshot->incotermCode = $entity->getIncoterm();
        $snapshot->incotermName = $entity->getIncoterm();
        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updateWarehouseDetails(DocSnapshot $snapshot, NmtInventoryWarehouse $entity)
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
        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtFinPostingPeriod $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updatePostingPeriodDetails(DocSnapshot $snapshot, NmtFinPostingPeriod $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->postingPeriod = $entity->getId();
        $snapshot->postingPeriodId = $entity->getId();
        // $snapshot->postingPeriodFrom = $entity->getPostingFromDate();
        // $snapshot->postingPeriodTo = $entity->getPostingToDate();
        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationPmtTerm $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updatePmtTermDetails(DocSnapshot $snapshot, NmtApplicationPmtTerm $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->pmtTerm = $entity->getId();
        $snapshot->paymentTermName = $entity->getPmtTermName();
        $snapshot->paymentTermCode = $entity->getPmtTermCode();

        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationCurrency $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updateLocalCurrencyDetails(DocSnapshot $snapshot, NmtApplicationCurrency $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->localCurrency = $entity->getId();
        $snapshot->localCurrencyId = $snapshot->localCurrency;
        $snapshot->localCurrencyISO = $entity->getCurrency();

        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationCurrency $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updateDocCurrencyDetails(DocSnapshot $snapshot, NmtApplicationCurrency $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->docCurrency = $entity->getId();
        $snapshot->currencyIso3 = $entity->getCurrency();
        $snapshot->docCurrencyId = $snapshot->docCurrency;
        $snapshot->docCurrencyISO = $snapshot->getCurrencyIso3();

        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationPmtMethod $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updatePmtMethodDetails(DocSnapshot $snapshot, NmtApplicationPmtMethod $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->paymentMethod = $entity->getId();
        $snapshot->paymentMethodName = $entity->getMethodName();
        $snapshot->paymentMethodCode = $entity->getMethodCode();

        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtApplicationCompany $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updateCompanyDetails(DocSnapshot $snapshot, NmtApplicationCompany $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->company = $entity->getId();
        $snapshot->companyName = $entity->getCompanyName();
        $snapshot->companyCode = $entity->getCompanyCode();
        $snapshot->companyToken = $entity->getToken();
        return $snapshot;
    }

    /**
     *
     * @param \Procure\Domain\DocSnapshot $snapshot
     * @param \Application\Entity\NmtBpVendor $entity
     * @return NULL|\Procure\Domain\DocSnapshot
     */
    public static function updateVendorDetails(DocSnapshot $snapshot, NmtBpVendor $entity)
    {
        if ($snapshot == null) {
            return null;
        }

        if ($entity == null) {
            return $snapshot;
        }

        $snapshot->vendor = $entity->getId();
        $snapshot->vendorName = $entity->getVendorName();
        $snapshot->vendorId = $snapshot->vendor;
        $snapshot->vendorToken = $entity->getToken();
        $snapshot->vendorAddress = sprintf("%s %s", $entity->getStreet(), $entity->getCity());

        if ($entity->getCountry() !== null) {
            $snapshot->vendorCountry = $entity->getCountry()->getCountryName();
        }

        return $snapshot;
    }
}
