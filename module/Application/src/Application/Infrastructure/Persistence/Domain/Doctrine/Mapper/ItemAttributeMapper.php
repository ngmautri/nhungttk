<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Domain\Company\ItemAttribute\AttributeGroupSnapshot;
use Application\Domain\Company\ItemAttribute\AttributeSnapshot;
use Application\Entity\NmtInventoryAttribute;
use Application\Entity\NmtInventoryAttributeGroup;
use Doctrine\ORM\EntityManager;

class ItemAttributeMapper
{

    public static function mapAttributeGroupEntity(EntityManager $doctrineEM, AttributeGroupSnapshot $snapshot, NmtInventoryAttributeGroup $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setGroupCode($snapshot->groupCode);
        $entity->setGroupName($snapshot->groupName);
        $entity->setGroupName1($snapshot->groupName1);
        $entity->setRemarks($snapshot->remarks);
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setParentCode($snapshot->parentCode);
        $entity->setCanHaveLeaf($snapshot->canHaveLeaf);
        $entity->setIsActive($snapshot->isActive);

        // Mapping Date
        // =====================
        // $entity->setCreatedOn($snapshot->createdOn);
        // $entity->setLastChangeOn($snapshot->lastChangeOn);

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->lastChangeOn));
        }

        // Mapping Reference
        // =====================
        /*
         * $entity->setCompany($snapshot->company);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
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

    public static function mapAttributeEntity(EntityManager $doctrineEM, AttributeSnapshot $snapshot, NmtInventoryAttribute $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setAttributeCode($snapshot->attributeCode);
        $entity->setAttributeName($snapshot->attributeName);
        $entity->setAttributeName1($snapshot->attributeName1);
        $entity->setAttributeName2($snapshot->attributeName2);
        $entity->setCombinedName($snapshot->combinedName);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setRemarks($snapshot->remarks);
        $entity->setGroup($snapshot->group);

        // Mapping Date
        // =====================
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         *
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

        return $entity;
    }

    public static function createAttributeGroupSnapshot(EntityManager $doctrineEM, NmtInventoryAttributeGroup $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new AttributeGroupSnapshot();

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->groupCode = $entity->getGroupCode();
        $snapshot->groupName = $entity->getGroupName();
        $snapshot->groupName1 = $entity->getGroupName1();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->parentCode = $entity->getParentCode();
        $snapshot->canHaveLeaf = $entity->getCanHaveLeaf();
        $snapshot->isActive = $entity->getIsActive();

        // Mapping Date
        // =====================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         *
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
         * $snapshot->company = $entity->getCompany();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
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

    public static function createAttributetSnapshot(EntityManager $doctrineEM, NmtInventoryAttribute $entity)
    {
        if ($entity == null) {
            return null;
        }

        $snapshot = new AttributeSnapshot();
        // $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->attributeCode = $entity->getAttributeCode();
        $snapshot->attributeName = $entity->getAttributeName();
        $snapshot->attributeName1 = $entity->getAttributeName1();
        $snapshot->attributeName2 = $entity->getAttributeName2();
        $snapshot->combinedName = $entity->getCombinedName();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->group = $entity->getGroup();

        // Mapping Date
        // =====================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         *
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
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }
        return $snapshot;
    }
}
