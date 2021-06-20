<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryItemVariant;
use Application\Entity\NmtInventoryItemVariantAttribute;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\Variant\VariantAttributeSnapshot;
use Inventory\Domain\Item\Variant\VariantSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemSerialMapper
{

    /*
     * |=============================
     * |Mapping Variant
     * |
     * |=============================
     */

    /**
     *
     * @param EntityManager $doctrineEM
     * @param VariantSnapshot $snapshot
     * @param NmtInventoryItemVariant $entity
     * @return NULL|\Application\Entity\NmtInventoryItemVariant
     */
    public static function mapVariantEntity(EntityManager $doctrineEM, VariantSnapshot $snapshot, NmtInventoryItemVariant $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        /*
         * |=============================
         * | Mapping None-Object Field
         * | not mapping setID
         * |=============================
         */

        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setCombinedName($snapshot->combinedName);
        $entity->setPrice($snapshot->price);
        $entity->setIsActive($snapshot->isActive);
        $entity->setUpc($snapshot->upc);
        $entity->setEan13($snapshot->ean13);
        $entity->setBarcode($snapshot->barcode);
        $entity->setWeight($snapshot->weight);
        $entity->setRemarks($snapshot->remarks);
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setCbm($snapshot->cbm);
        $entity->setVariantCode($snapshot->variantCode);
        $entity->setVariantName($snapshot->variantName);
        $entity->setVariantAlias($snapshot->variantAlias);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setFullCombinedName($snapshot->fullCombinedName);
        $entity->setItemName($snapshot->itemName);
        $entity->setVariantSku($snapshot->variantSku);

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */

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

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */
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
     * @param NmtInventoryItemVariant $entity
     * @param VariantSnapshot $snapshot
     * @param boolean $needDetails
     * @return NULL|\Inventory\Domain\Item\Variant\VariantSnapshot
     */
    public static function createVariantSnapshot(NmtInventoryItemVariant $entity, VariantSnapshot $snapshot = null, $needDetails = false)
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
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->cbm = $entity->getCbm();
        $snapshot->variantCode = $entity->getVariantCode();
        $snapshot->variantName = $entity->getVariantName();
        $snapshot->variantAlias = $entity->getVariantAlias();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->fullCombinedName = $entity->getFullCombinedName();
        // $snapshot->itemName = $entity->getItemName();
        $snapshot->variantSku = $entity->getVariantSku();

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

        if ($entity->getItem() != null) {
            $snapshot->item = $entity->getItem()->getId();
            $snapshot->itemName = $entity->getItem()->getItemName();
        }

        return $snapshot;
    }

    /*
     * |=============================
     * |Mapping Variant-Attribute
     * |
     * |=============================
     */

    /**
     *
     * @param EntityManager $doctrineEM
     * @param VariantAttributeSnapshot $snapshot
     * @param NmtInventoryItemVariantAttribute $entity
     * @return NULL|\Application\Entity\NmtInventoryItemVariantAttribute
     */
    public static function mapVariantAttributeEntity(EntityManager $doctrineEM, VariantAttributeSnapshot $snapshot, NmtInventoryItemVariantAttribute $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        // $entity->setId($snapshot->id);
        $entity->setUuid($snapshot->uuid);
        $entity->setRemarks($snapshot->remarks);
        $entity->setRevisionNo($snapshot->revisionNo);
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
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         *
         * $entity->setAttribute($snapshot->attribute);
         * $entity->setVariant($snapshot->variant);
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

        if ($snapshot->variant > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryItemVariant $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryItemVariant')->find($snapshot->variant);
            $entity->setVariant($obj);
        }

        if ($snapshot->attribute > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryAttribute $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryAttribute')->find($snapshot->attribute);
            $entity->setAttribute($obj);
        }

        return $entity;
    }

    /**
     *
     * @param NmtInventoryItemVariantAttribute $entity
     * @param VariantAttributeSnapshot $snapshot
     * @param boolean $needDetails
     * @return NULL|\Inventory\Domain\Item\Variant\VariantAttributeSnapshot
     */
    public static function createVariantAttributeSnapshot(NmtInventoryItemVariantAttribute $entity, VariantAttributeSnapshot $snapshot = null, $needDetails = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new VariantAttributeSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->revisionNo = $entity->getRevisionNo();
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
         * $snapshot->attribute = $entity->getAttribute();
         * $snapshot->variant = $entity->getVariant();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         */
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getVariant() !== null) {
            $snapshot->variant = $entity->getVariant()->getId();
        }

        if ($entity->getAttribute() !== null) {
            $snapshot->attribute = $entity->getAttribute()->getId();
        }

        return $snapshot;
    }
}
