<?php
namespace Application\Infrastructure\Mapper;

use Application\Domain\Company\CompanySnapshot;
use Application\Entity\NmtApplicationCompany;
use Doctrine\ORM\EntityManager;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param CompanySnapshot $snapshot
     * @param NmtApplicationCompany $entity
     * @return NULL|\Application\Entity\NmtApplicationCompany
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, CompanySnapshot $snapshot, NmtApplicationCompany $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        $entity->setId($snapshot->id);
        $entity->setCompanyCode($snapshot->companyCode);
        $entity->setCompanyName($snapshot->companyName);
        $entity->setDefaultLogoId($snapshot->defaultLogoId);
        $entity->setStatus($snapshot->status);
        $entity->setIsDefault($snapshot->isDefault);
        $entity->setToken($snapshot->token);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setUuid($snapshot->uuid);
        $entity->setDefaultLocale($snapshot->defaultLocale);
        $entity->setDefaultLanguage($snapshot->defaultLanguage);
        $entity->setDefaultFormat($snapshot->defaultFormat);
        $entity->setDefaultWarehouseCode($snapshot->defaultWarehouseCode);
        $entity->setDefaultCurrencyIso($snapshot->defaultCurrencyIso);
        $entity->setDefaultCurrency($snapshot->defaultCurrency);

        // Mapping Date
        // =====================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         */
        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        // Mapping Reference
        // =====================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setCountry($snapshot->country);
         * $entity->setDefaultAddress($snapshot->defaultAddress);
         * $entity->setDefaultWarehouse($snapshot->defaultWarehouse);
         */

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setPmtTerm($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }

        if ($snapshot->country > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCountry $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->find($snapshot->country);
            $entity->setCountry($obj);
        }

        if ($snapshot->defaultAddress > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompanyAddress $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompanyAddress')->find($snapshot->defaultAddress);
            $entity->setDefaultAddress($obj);
        }

        if ($snapshot->defaultWarehouse > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($snapshot->defaultWarehouse);
            $entity->setDefaultWarehouse($obj);
        }

        return $entity;
    }

    /**
     *
     * @param NmtApplicationCompany $entity
     * @return NULL|\Application\Domain\Company\CompanyDetailsSnapshot
     */
    public static function createSnapshot(NmtApplicationCompany $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new CompanySnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->companyCode = $entity->getCompanyCode();
        $snapshot->companyName = $entity->getCompanyName();
        $snapshot->defaultLogoId = $entity->getDefaultLogoId();
        $snapshot->status = $entity->getStatus();
        $snapshot->isDefault = $entity->getIsDefault();
        $snapshot->token = $entity->getToken();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->defaultLocale = $entity->getDefaultLocale();
        $snapshot->defaultLanguage = $entity->getDefaultLanguage();
        $snapshot->defaultFormat = $entity->getDefaultFormat();
        $snapshot->defaultCurrency = $entity->getDefaultCurrency();

        // Mapping Date
        // =====================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         *
         */
        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================

        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->country = $entity->getCountry();
         * $snapshot->defaultAddress = $entity->getDefaultAddress();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->defaultWarehouse = $entity->getDefaultWarehouse();
         */

        $snapshot->defaultCurrency = $entity->getDefaultCurrency();
        if ($entity->getDefaultCurrency() !== null) {
            $snapshot->defaultCurrency = $entity->getDefaultCurrency()->getId();
            $snapshot->defaultCurrencyIso = $entity->getDefaultCurrency()->getCurrency();
        }

        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->country = $entity->getCountry();
        if ($entity->getCountry() !== null) {
            $snapshot->country = $entity->getCountry()->getId();
        }

        $snapshot->defaultAddress = $entity->getDefaultAddress();
        if ($entity->getDefaultAddress() !== null) {
            $snapshot->defaultAddress = $entity->getDefaultAddress()->getId();
        }

        $snapshot->lastChangeBy = $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        $snapshot->defaultWarehouse = $entity->getDefaultWarehouse();
        if ($entity->getDefaultWarehouse() !== null) {
            $snapshot->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
            $snapshot->defaultWarehouseCode = $entity->getDefaultWarehouse()->getWhCode();
        }
        return $snapshot;
    }
}
