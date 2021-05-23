<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemAttribute;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Variant\VariantSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeMapper
{

    public static function mapSnapshotEntity(EntityManager $doctrineEM, ItemSnapshot $snapshot, NmtInventoryItem $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setCombinedName($snapshot->combinedName);
        $entity->setPrice($snapshot->price);
        $entity->setIsActive($snapshot->isActive);
        $entity->setUpc($snapshot->upc);
        $entity->setEan13($snapshot->ean13);
        $entity->setBarcode($snapshot->barcode);
        $entity->setWeight($snapshot->weight);
        $entity->setRemarks($snapshot->remarks);

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
         * $entity->setItem($snapshot->item);
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

        if ($snapshot->item > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->item);
            $entity->setItem($obj);
        }

        return $entity;
    }

    /**
     *
     * @param NmtInventoryItemAttribute $entity
     * @param VariantSnapshot $snapshot
     * @param boolean $needDetails
     * @return NULL|\Inventory\Domain\Item\Variant\VariantSnapshot
     */
    public static function createSnapshot(NmtInventoryItemAttribute $entity, VariantSnapshot $snapshot = null, $needDetails = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new VariantSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->combinedName = $entity->getCombinedName();
        $snapshot->price = $entity->getPrice();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->upc = $entity->getUpc();
        $snapshot->ean13 = $entity->getEan13();
        $snapshot->barcode = $entity->getBarcode();
        $snapshot->weight = $entity->getWeight();
        $snapshot->remarks = $entity->getRemarks();

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

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getItem() !== null) {
            $snapshot->item = $entity->getItem()->getId();
        }

        return $snapshot;
    }
}
