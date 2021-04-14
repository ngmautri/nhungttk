<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Domain\Company\CompanySnapshot;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationDepartment;
use Doctrine\ORM\EntityManager;

class DepartmentMapper
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
    public static function createSnapshot(NmtApplicationDepartment $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new DepartmentSnapshot();
        $snapshot->nodeId = $entity->getNodeId();
        $snapshot->nodeName = $entity->getNodeName();
        $snapshot->nodeParentId = $entity->getNodeParentId();
        $snapshot->path = $entity->getPath();
        $snapshot->pathDepth = $entity->getPathDepth();
        $snapshot->status = $entity->getStatus();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->departmentName = $entity->getDepartmentName();
        $snapshot->departmentCode = $entity->getDepartmentCode();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->departmentNameLocal = $entity->getDepartmentNameLocal();

        // Override
        $snapshot->departmentName = $entity->getNodeName();
        $snapshot->departmentCode = $entity->getNodeId();

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
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->company = $entity->getCompany();
         */

        $snapshot->createdBy = $entity->getCreatedBy();
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        $snapshot->lastChangeBy = $entity->getLastChangeBy();
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }
        return $snapshot;
    }
}
