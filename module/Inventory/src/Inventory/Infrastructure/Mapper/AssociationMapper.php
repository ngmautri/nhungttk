<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryAssociationItem;
use Application\Entity\NmtInventoryItem;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Association\AssociationSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param AssociationSnapshot $snapshot
     * @param NmtInventoryItem $entity
     * @return NULL|\Application\Entity\NmtInventoryItem
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, AssociationSnapshot $snapshot, NmtInventoryAssociationItem $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================
        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setIsActive($snapshot->isActive);
        $entity->setHasBothDirection($snapshot->hasBothDirection);
        $entity->setRemarks($snapshot->remarks);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setVersion($snapshot->version);

        // ============================
        // DATE MAPPING
        // ============================
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

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setAssociation($snapshot->association);
         * $entity->setMainItem($snapshot->mainItem);
         * $entity->setRelatedItem($snapshot->relatedItem);
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         */
        if ($snapshot->association > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryAssociation $obj ;
             */

            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryAssociation')->find($snapshot->association);
            $entity->setAssociation($obj);
        }

        if ($snapshot->mainItem > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->mainItem);
            $entity->setMainItem($obj);
        }

        if ($snapshot->relatedItem > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->relatedItem);
            $entity->setRelatedItem($obj);
        }

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
     * @param NmtInventoryAssociationItem $entity
     * @param AssociationSnapshot $snapshot
     * @param boolean $needDetails
     * @return NULL|\Inventory\Domain\Association\AssociationSnapshot
     */
    public static function createSnapshot(NmtInventoryAssociationItem $entity, AssociationSnapshot $snapshot = null, $needDetails = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new AssociationSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->hasBothDirection = $entity->getHasBothDirection();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->version = $entity->getVersion();

        // ============================
        // DATE MAPPING
        // ============================

        // $snapshot->createdOn = $entity->getCreatedOn();
        // $snapshot->lastChangeOn = $entity->getLastChangeOn();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        /*
         * $snapshot->association = $entity->getAssociation();
         * $snapshot->mainItem = $entity->getMainItem();
         * $snapshot->relatedItem = $entity->getRelatedItem();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         */

        if ($entity->getAssociation() !== null) {
            $snapshot->association = $entity->getAssociation()->getId();
        }

        if ($entity->getMainItem() !== null) {
            $snapshot->mainItem = $entity->getMainItem()->getId();
        }

        if ($entity->getRelatedItem() !== null) {
            $snapshot->relatedItem = $entity->getRelatedItem()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        return $snapshot;
    }
}
