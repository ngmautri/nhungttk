<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Mapper;

use Application\Entity\NmtProcureClearingDoc;
use Application\Entity\NmtProcureClearingRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\Clearing\ClearingDocSnapshot;
use Procure\Domain\Clearing\ClearingRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param ClearingDocSnapshot $snapshot
     * @param NmtProcureClearingDoc $entity
     * @return NULL|\Application\Entity\NmtProcureClearingDoc
     */
    public static function mapDocSnapshotEntity(EntityManager $doctrineEM, ClearingDocSnapshot $snapshot, NmtProcureClearingDoc $entity)
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
        $entity->setVersion($snapshot->version);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDocType($snapshot->docType);
        $entity->setDocNumber($snapshot->docNumber);
        $entity->setSysNumber($snapshot->sysNumber);

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */
        /*
         * $entity->setDocDate($snapshot->docDate);
         * $entity->setLastChangeOn($snapshot->lastChangeOn);
         */

        if ($snapshot->createdOn !== null) {
            $entity->setCreatedOn(new \DateTime($snapshot->createdOn));
        }

        if ($snapshot->docDate !== null) {
            $entity->setDocDate(new \DateTime($snapshot->docDate));
        }

        if ($snapshot->lastChangeOn !== null) {
            $entity->setLastChangeBy(new \DateTime($snapshot->lastChangeOn));
        }

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */

        /*
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

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param ClearingRowSnapshot $snapshot
     * @param NmtProcureClearingRow $entity
     * @return NULL|\Application\Entity\NmtProcureClearingRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, ClearingRowSnapshot $snapshot, NmtProcureClearingRow $entity)
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
        $entity->setRtRow($snapshot->rtRow);
        $entity->setClearingStandardQuantity($snapshot->clearingStandardQuantity);
        $entity->setRemarks($snapshot->remarks);
        $entity->setRowIdentifer($snapshot->rowIdentifer);
        $entity->setRevisionNo($snapshot->revisionNo);
        $entity->setDocVersion($snapshot->docVersion);

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
         * $entity->setDoc($snapshot->doc);
         * $entity->setPrRow($snapshot->prRow);
         * $entity->setQoRow($snapshot->qoRow);
         * $entity->setPoRow($snapshot->poRow);
         * $entity->setGrRow($snapshot->grRow);
         * $entity->setApRow($snapshot->apRow);
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

        if ($snapshot->doc > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureClearingDoc $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureClearingDoc')->find($snapshot->doc);
            $entity->setDoc($obj);
        }

        if ($snapshot->prRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePrRow')->find($snapshot->prRow);
            $entity->setPrRow($obj);
        }

        if ($snapshot->qoRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureQoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureQoRow')->find($snapshot->qoRow);
            $entity->setQoRow($obj);
        }

        if ($snapshot->poRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcurePoRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcurePoRow')->find($snapshot->poRow);
            $entity->setPoRow($obj);
        }

        if ($snapshot->grRow > 0) {
            /**
             *
             * @var \Application\Entity\NmtProcureGrRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\NmtProcureGrRow')->find($snapshot->grRow);
            $entity->setGrRow($obj);
        }

        if ($snapshot->apRow > 0) {
            /**
             *
             * @var \Application\Entity\FinVendorInvoiceRow $obj ;
             */
            $obj = $doctrineEM->getRepository('Application\Entity\FinVendorInvoiceRow')->find($snapshot->apRow);
            $entity->setApRow($obj);
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtProcureClearingDoc $entity
     * @param
     *            $snapshot
     * @param boolean $needDetails
     * @return NULL|string|\Procure\Domain\Clearing\ClearingDocSnapshot
     */
    public static function createDocSnapshot(EntityManager $doctrineEM, NmtProcureClearingDoc $entity, $snapshot = null, $needDetails = true)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }

        if (! $snapshot instanceof ClearingDocSnapshot) {
            $snapshot = new ClearingDocSnapshot();
        }
        /*
         * |=============================
         * | Mapping None-Object Field
         * |=============================
         */

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->createdOn = $entity->getCreatedOn();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docType = $entity->getDocType();
        $snapshot->docNumber = $entity->getDocNumber();
        $snapshot->sysNumber = $entity->getSysNumber();

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */
        /*
         * $snapshot->createdOn = $entity->getCreatedOn();
         * $snapshot->docDate = $entity->getDocDate();
         * $snapshot->lastChangeOn = $entity->getLastChangeOn();
         */

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        if (! $entity->getDocDate() == null) {
            $snapshot->docDate = $entity->getDocDate()->format("Y-m-d H:i:s");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d H:i:s");
        }

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */

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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param NmtProcureClearingRow $entity
     * @return NULL|\Procure\Domain\Clearing\ClearingRowSnapshot
     */
    public static function createRowSnapshot(EntityManager $doctrineEM, NmtProcureClearingRow $entity)
    {
        if ($entity == null || $doctrineEM == null) {
            return null;
        }
        $snapshot = new ClearingRowSnapshot();
        /*
         * |=============================
         * | Mapping None-Object Field
         * |
         * |=============================
         */

        $snapshot->id = $entity->getId();
        $snapshot->token = $entity->getToken();
        $snapshot->rtRow = $entity->getRtRow();
        $snapshot->clearingStandardQuantity = $entity->getClearingStandardQuantity();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->rowIdentifer = $entity->getRowIdentifer();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->docVersion = $entity->getDocVersion();

        /*
         * |=============================
         * | DATE MAPPING
         * |
         * |=============================
         */
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

        /*
         * |=============================
         * | REFERRENCE MAPPING
         * |
         * |=============================
         */
        /*
         * $snapshot->doc = $entity->getDoc();
         * $snapshot->prRow = $entity->getPrRow();
         * $snapshot->qoRow = $entity->getQoRow();
         * $snapshot->poRow = $entity->getPoRow();
         * $snapshot->grRow = $entity->getGrRow();
         * $snapshot->apRow = $entity->getApRow();
         * $snapshot->createdBy = $entity->getCreatedBy();
         * $snapshot->lastChangeBy = $entity->getLastChangeBy();
         */

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getDoc() !== null) {
            $snapshot->doc = $entity->getDoc()->getId();
        }

        if ($entity->getPrRow() !== null) {
            $snapshot->prRow = $entity->getPrRow()->getId();
        }

        if ($entity->getQoRow() !== null) {
            $snapshot->prRow = $entity->getQoRow()->getId();
        }

        if ($entity->getPoRow() !== null) {
            $snapshot->poRow = $entity->getPoRow()->getId();
        }

        if ($entity->getGrRow() !== null) {
            $snapshot->grRow = $entity->getGrRow()->getId();
        }

        if ($entity->getApRow() !== null) {
            $snapshot->apRow = $entity->getApRow()->getId();
        }

        return $snapshot;
    }
}
