<?php
namespace Inventory\Infrastructure\Mapper;

use Application\Entity\NmtInventoryWarehouse;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\WarehouseSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WhMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param WarehouseSnapshot $snapshot
     * @param NmtInventoryWarehouse $entity
     * @return NULL|\Application\Entity\NmtInventoryWarehouse
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, WarehouseSnapshot $snapshot, NmtInventoryWarehouse $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        $entity->setId($snapshot->id);
        $entity->setWhCode($snapshot->whCode);
        $entity->setWhName($snapshot->whName);
        $entity->setWhAddress($snapshot->whAddress);
        $entity->setWhContactPerson($snapshot->whContactPerson);
        $entity->setWhTelephone($snapshot->whTelephone);
        $entity->setWhEmail($snapshot->whEmail);
        $entity->setIsLocked($snapshot->isLocked);
        $entity->setWhStatus($snapshot->whStatus);
        $entity->setRemarks($snapshot->remarks);
        $entity->setIsDefault($snapshot->isDefault);
        $entity->setSysNumber($snapshot->sysNumber);
        $entity->setToken($snapshot->token);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setUuid($snapshot->uuid);

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

        if ($snapshot->lastchangeOn !== null) {
            $entity->setLastchangeOn(new \DateTime($snapshot->lastchangeOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setCompany($snapshot->company);
         * $entity->setWhCountry($snapshot->whCountry);
         * $entity->setLastChangeBy($snapshot->lastChangeBy);
         * $entity->setStockkeeper($snapshot->stockkeeper);
         * $entity->setWhController($snapshot->whController);
         * $entity->setLocation($snapshot->location);
         */

        // =========

        if ($snapshot->createdBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->createdBy);
            $entity->setCreatedBy($obj);
        }

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->whCountry > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCountry $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->find($snapshot->whCountry);
            $entity->setWhCountry($obj);
        }

        if ($snapshot->lastChangeBy > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->lastChangeBy);
            $entity->setLastChangeBy($obj);
        }
        if ($snapshot->stockkeeper > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->stockkeeper);
            $entity->setStockkeeper($obj);
        }

        if ($snapshot->whController > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->whController);
            $entity->setWhController($obj);
        }

        if ($snapshot->location > 0) {
            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouseLocation $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->find($snapshot->location);
            $entity->setLocation($obj);
        }

        return $entity;
    }

    public static function createSnapshot(EntityManager $doctrineEM, NmtInventoryWarehouse $entity, $snapshot = null)
    {
        if ($entity == null) {
            return null;
        }

        if ($snapshot == null) {
            $snapshot = new WarehouseSnapshot();
        }

        // =================================
        // Mapping None-Object Field
        // =================================

        foreach ($entity->getLocationList() as $loc) {
            echo $loc->getLocationName();
        }

        $snapshot->id = $entity->getId();
        $snapshot->whCode = $entity->getWhCode();
        $snapshot->whName = $entity->getWhName();
        $snapshot->whAddress = $entity->getWhAddress();
        $snapshot->whContactPerson = $entity->getWhContactPerson();
        $snapshot->whTelephone = $entity->getWhTelephone();
        $snapshot->whEmail = $entity->getWhEmail();
        $snapshot->isLocked = $entity->getIsLocked();
        $snapshot->whStatus = $entity->getWhStatus();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isDefault = $entity->getIsDefault();
        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->token = $entity->getToken();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->uuid = $entity->getUuid();

        // ============================
        // DATE MAPPING
        // ============================
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         */

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
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->company = $entity->getCompany();
         * $snapshot->whCountry = $entity->getWhCountry();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         * $snapshot->stockkeeper = $entity->getStockkeeper();
         * $snapshot->whController = $entity->getWhController();
         * $snapshot->location = $entity->getLocation();
         */
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getStockkeeper() !== null) {
            $snapshot->stockkeeper = $entity->getStockkeeper()->getId();
        }
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getWhController() !== null) {
            $snapshot->whController = $entity->getWhController()->getId();
        }

        if ($entity->getLocation() !== null) {
            $snapshot->location = $entity->getLocation()->getId();
        }

        return $snapshot;
    }
}
