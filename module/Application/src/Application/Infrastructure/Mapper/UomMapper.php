<?php
namespace Application\Infrastructure\Mapper;

use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Entity\NmtApplicationUom;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param UomSnapshot $snapshot
     * @param NmtApplicationUom $entity
     * @return NULL|\Application\Entity\NmtApplicationUom
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, UomSnapshot $snapshot, NmtApplicationUom $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        // $entity->setId($snapshot->id);
        $entity->setUomCode($snapshot->uomCode);
        $entity->setUomName($snapshot->uomName);
        $entity->setUomDescription($snapshot->uomDescription);
        $entity->setConversionFactor($snapshot->conversionFactor);
        $entity->setSector($snapshot->sector);
        $entity->setSymbol($snapshot->symbol);
        $entity->setStatus($snapshot->status);

        // ============================
        // DATE MAPPING
        // ============================
        // $entity->setCreatedOn($snapshot->createdOn);

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================
        /*
         * $entity->setCreatedBy($snapshot->createdBy);
         * $entity->setCompany($snapshot->company);
         */
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

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtApplicationUom $entity
     * @param UomSnapshot $snapshot
     * @return NULL|\Application\Domain\Shared\Uom\UomSnapshot
     */
    public static function createSnapshot(EntityManager $doctrineEM, NmtApplicationUom $entity, UomSnapshot $snapshot)
    {
        if ($entity == null || $snapshot == null) {
            return null;
        }

        // =================================
        // Mapping None-Object Field
        // =================================
        $snapshot->id = $entity->getId();
        $snapshot->uomCode = $entity->getUomCode();
        $snapshot->uomName = $entity->getUomName();
        $snapshot->uomDescription = $entity->getUomDescription();
        $snapshot->conversionFactor = $entity->getConversionFactor();
        $snapshot->sector = $entity->getSector();
        $snapshot->symbol = $entity->getSymbol();
        $snapshot->status = $entity->getStatus();

        // ============================
        // DATE MAPPING
        // ============================
        // $snapshot->createdOn = $entity->getCreatedOn();

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        // ============================
        // REFERRENCE MAPPING
        // ============================

        // $snapshot->createdBy = $entity->getCreatedBy();
        // $snapshot->company = $entity->getCompany();

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
            $entity->setCreatedBy($obj);
        }
        return $snapshot;
    }
}