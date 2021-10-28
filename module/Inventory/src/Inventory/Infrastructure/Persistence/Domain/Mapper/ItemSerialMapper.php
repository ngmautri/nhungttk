<?php
namespace Inventory\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Entity\NmtInventoryItemSerial;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\Serial\SerialSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialMapper
{

    /*
     * |=============================
     * |Mapping Serial
     * |
     * |=============================
     */
    public static function mapSerialEntity(EntityManager $doctrineEM, SerialSnapshot $snapshot, NmtInventoryItemSerial $entity)
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

        $entity->setToken($snapshot->token);
        $entity->setSerialNumber($snapshot->serialNumber);
        $entity->setIsActive($snapshot->isActive);
        $entity->setRemarks($snapshot->remarks);
        $entity->setMfgSerialNumber($snapshot->mfgSerialNumber);
        $entity->setLotNumber($snapshot->lotNumber);
        $entity->setItemName($snapshot->itemName);
        $entity->setLocation($snapshot->location);
        $entity->setCategory($snapshot->category);
        $entity->setMfgName($snapshot->mfgName);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setSerialNumber1($snapshot->serialNumber1);
        $entity->setSerialNumber2($snapshot->serialNumber2);
        $entity->setSerialNumber3($snapshot->serialNumber3);
        $entity->setMfgModel($snapshot->mfgModel);
        $entity->setMfgModel1($snapshot->mfgModel1);
        $entity->setMfgModel2($snapshot->mfgModel2);
        $entity->setMfgDescription($snapshot->mfgDescription);
        $entity->setCapacity($snapshot->capacity);
        $entity->setErpAssetNumber($snapshot->erpAssetNumber);
        $entity->setErpAssetNumber1($snapshot->erpAssetNumber1);
        $entity->setIsReversed($snapshot->isReversed);
        $entity->setReversalDoc($snapshot->reversalDoc);
        $entity->setReversalReason($snapshot->reversalReason);
        $entity->setIsReversable($snapshot->isReversable);
        $entity->setUuid($snapshot->uuid);

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */
        /*
         * $entity->setCreatedOn($snapshot->createdOn);
         * $entity->setLastchangeOn($snapshot->lastchangeOn);
         *
         * $entity->setConsumedOn($snapshot->consumedOn);
         * $entity->setMfgDate($snapshot->mfgDate);
         *
         * $entity->setMfgWarrantyStart($snapshot->mfgWarrantyStart);
         * $entity->setMfgWarrantyEnd($snapshot->mfgWarrantyEnd);
         * $entity->setReversalDate($snapshot->reversalDate);
         */

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        if ($snapshot->consumedOn !== null) {
            $entity->setLastChangeOn(new \DateTime($snapshot->consumedOn));
        }

        if ($snapshot->mfgDate !== null) {
            $entity->setMfgDate(new \DateTime($snapshot->mfgDate));
        }

        if ($snapshot->mfgWarrantyStart !== null) {
            $entity->setMfgDate(new \DateTime($snapshot->mfgWarrantyStart));
        }

        if ($snapshot->mfgWarrantyEnd !== null) {
            $entity->setMfgWarrantyStart(new \DateTime($snapshot->mfgWarrantyEnd));
        }

        if ($snapshot->setReversalDate !== null) {
            $entity->setReversalDate(new \DateTime($snapshot->setReversalDate));
        }

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setLastchangeBy($snapshot->lastchangeBy);
         *
         * $entity->setItem($snapshot->item);
         * $entity->setSerial($snapshot->serial);
         * $entity->setInventoryTrx($snapshot->inventoryTrx);
         * $entity->setApRow($snapshot->apRow);
         * $entity->setGrRow($snapshot->grRow);
         * $entity->setOriginCountry($snapshot->originCountry);
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

        if ($snapshot->inventoryTrx > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryTrx $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryTrx')->find($snapshot->inventoryTrx);
            $entity->setInventoryTrx($obj);
        }

        if ($snapshot->apRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->apRow);
            $entity->setApRow($obj);
        }

        if ($snapshot->grRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->grRow);
            $entity->setGrRow($obj);
        }

        if ($snapshot->originCountry > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCountry $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->find($snapshot->originCountry);
            $entity->setOriginCountry($obj);
        }

        return $entity;
    }

    public static function createSerialSnapshot(NmtInventoryItemSerial $entity, SerialSnapshot $snapshot = null, $needDetails = false)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new SerialSnapshot();
        }

        /*
         * |=============================
         * | None-Object Field
         * |
         * |=============================
         */

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->serialNumber = $entity->getSerialNumber();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->mfgSerialNumber = $entity->getMfgSerialNumber();
        $snapshot->lotNumber = $entity->getLotNumber();
        $snapshot->itemName = $entity->getItemName();
        $snapshot->location = $entity->getLocation();
        $snapshot->category = $entity->getCategory();
        $snapshot->mfgName = $entity->getMfgName();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->serialNumber1 = $entity->getSerialNumber1();
        $snapshot->serialNumber2 = $entity->getSerialNumber2();
        $snapshot->serialNumber3 = $entity->getSerialNumber3();
        $snapshot->mfgModel = $entity->getMfgModel();
        $snapshot->mfgModel1 = $entity->getMfgModel1();
        $snapshot->mfgModel2 = $entity->getMfgModel2();
        $snapshot->mfgDescription = $entity->getMfgDescription();
        $snapshot->capacity = $entity->getCapacity();
        $snapshot->erpAssetNumber = $entity->getErpAssetNumber();
        $snapshot->erpAssetNumber1 = $entity->getErpAssetNumber1();
        $snapshot->isReversed = $entity->getIsReversed();
        $snapshot->reversalDoc = $entity->getReversalDoc();
        $snapshot->reversalReason = $entity->getReversalReason();
        $snapshot->isReversable = $entity->getIsReversable();
        $snapshot->uuid = $entity->getUuid();

        /*
         * |=============================
         * | Mapping Date
         * |
         * |=============================
         */

        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         * $snapshot->consumedOn = $entity->getConsumedOn();
         * $snapshot->mfgDate = $entity->getMfgDate();
         * $snapshot->mfgWarrantyStart = $entity->getMfgWarrantyStart();
         * $snapshot->mfgWarrantyEnd = $entity->getMfgWarrantyEnd();
         * $snapshot->reversalDate = $entity->getReversalDate();
         */
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getConsumedOn() == null) {
            $snapshot->consumedOn = $entity->getConsumedOn()->format("Y-m-d");
        }

        if (! $entity->getMfgDate() == null) {
            $snapshot->mfgDate = $entity->getMfgDate()->format("Y-m-d");
        }

        if (! $entity->getMfgWarrantyStart() == null) {
            $snapshot->mfgWarrantyStart = $entity->getMfgWarrantyStart()->format("Y-m-d");
        }

        if (! $entity->getMfgWarrantyEnd() == null) {
            $snapshot->mfgWarrantyEnd = $entity->getMfgWarrantyEnd()->format("Y-m-d");
        }

        if (! $entity->getReversalDate() == null) {
            $snapshot->reversalDate = $entity->getReversalDate()->format("Y-m-d");
        }

        /*
         * |=============================
         * |REFERRENCE MAPPING
         * |
         * |=============================
         */
        // $snapshot->createdBy = $entity->getCreatedBy();
        // $snapshot->lastchangeBy = $entity->getLastchangeBy();
        // $snapshot->item = $entity->getItem();
        // $snapshot->serial = $entity->getSerial();
        // $snapshot->inventoryTrx = $entity->getInventoryTrx();
        // $snapshot->apRow = $entity->getApRow();
        // $snapshot->grRow = $entity->getGrRow();
        // $snapshot->originCountry = $entity->getOriginCountry();

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getItem() != null) {
            $snapshot->item = $entity->getItem()->getId();
        }

        if ($entity->getInventoryTrx() != null) {
            $snapshot->inventoryTrx = $entity->getInventoryTrx()->getId();
        }

        if ($entity->getApRow() != null) {
            $snapshot->apRow = $entity->getApRow()->getId();
        }

        if ($entity->getGrRow() != null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }

        if ($entity->getOriginCountry() != null) {
            $snapshot->originCountry = $entity->getOriginCountry()->getId();
        }

        return $snapshot;
    }
}
