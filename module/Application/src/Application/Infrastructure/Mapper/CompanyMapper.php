<?php
namespace Application\Infrastructure\Mapper;

use Application\Domain\Company\CompanyDetailsSnapshot;
use Application\Domain\Company\CompanySnapshot;
use Application\Entity\NmtApplicationCompany;
use Doctrine\ORM\EntityManager;

/**
 *
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

        return $entity;
    }

    /**
     *
     * @param NmtApplicationCompany $entity
     * @return NULL|\Application\Domain\Company\CompanyDetailsSnapshot
     */
    public static function createDetailSnapshot(NmtApplicationCompany $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new CompanyDetailsSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->companyCode = $entity->getCompanyCode();
        $snapshot->companyName = $entity->getCompanyName();
        $snapshot->defaultLogoId = $entity->getDefaultLogoId();
        $snapshot->status = $entity->getStatus();
        $snapshot->isDefault = $entity->getIsDefault();
        $snapshot->token = $entity->getToken();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->uuid = $entity->getUuid();

        // Mapping Date
        // =====================

        // $snapshot->lastChangeOn = $entity->getLastChangeOn();

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // $snapshot->createdOn = $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        // Mapping Reference
        // =====================

        $snapshot->defaultCurrency = $entity->getDefaultCurrency();
        if ($entity->getDefaultCurrency() !== null) {
            $snapshot->defaultCurrency = $entity->getDefaultCurrency()->getId();
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
        }
        return $snapshot;
    }
}
