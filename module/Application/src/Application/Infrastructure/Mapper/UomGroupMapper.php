<?php
namespace Application\Infrastructure\Mapper;

use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Domain\Shared\Uom\UomPairSnapshot;
use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Entity\AppUomGroup;
use Application\Entity\AppUomGroupMember;
use Application\Entity\NmtApplicationUom;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomGroupMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param UomGroupSnapshot $snapshot
     * @param AppUomGroup $entity
     * @return NULL|\Application\Entity\AppUomGroup
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, UomGroupSnapshot $snapshot, AppUomGroup $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        // $entity->setUuid($snapshot->uuid);
        $entity->setGroupName($snapshot->groupName);
        $entity->setIsActive($snapshot->isActive);
        $entity->setCreatedOn($snapshot->createdOn);
        $entity->setBaseUom($snapshot->baseUom);

        // ============================
        // DATE MAPPING
        // ============================
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);

        $entity->setCreatedOn(new \DateTime());

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setCompany($snapshot->company);
         *
         */
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param AppUomGroup $entity
     * @param UomGroupSnapshot $snapshot
     * @return NULL|\Application\Domain\Shared\Uom\UomGroupSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, AppUomGroup $entity, UomGroupSnapshot $snapshot)
    {
        if ($entity == null || $snapshot == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->groupName = $entity->getGroupName();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->baseUom = $entity->getBaseUom();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->company = $entity->getCompany();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }
        return $snapshot;
    }

    public static function mapUomPairSnapshotEntity(EntityManager $doctrineEM, UomPairSnapshot $snapshot, AppUomGroupMember $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setUomId($snapshot->uomId);
        $entity->setBaseUomId($snapshot->baseUomId);
        $entity->setIsActive($snapshot->isActive);
        $entity->setRemarks($snapshot->remarks);
        $entity->setCounterUom($snapshot->counterUom);
        $entity->setConvertFactor($snapshot->convertFactor);
        $entity->setDescription($snapshot->description);
        $entity->setGroupName($snapshot->groupName);

        // ============================
        // DATE MAPPING
        // ============================
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);

        $entity->setCreatedOn(new \DateTime());

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setGroup($snapshot->group);
         *
         *
         */
        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }

        if ($snapshot->group > 0) {
            /**
             *
             * @var \Application\Entity\AppUomGroup $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\AppUomGroup')->find($snapshot->group);
            $entity->setGroup($obj);
        }

        return $entity;
    }

    public static function createUomPairSnapshot(EntityManager $doctrineEM, AppUomGroupMember $entity, UomSnapshot $snapshot)
    {
        if ($entity == null || $snapshot == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->uomId = $entity->getUomId();
        $snapshot->baseUomId = $entity->getBaseUomId();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->counterUom = $entity->getCounterUom();
        $snapshot->convertFactor = $entity->getConvertFactor();
        $snapshot->description = $entity->getDescription();
        $snapshot->groupName = $entity->getGroupName();
        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->group = $entity->getGroup();
         *
         */

        if ($entity->getGroup() !== null) {
            $snapshot->group = $entity->getGroup()->getId();
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }
        return $snapshot;
    }
}
