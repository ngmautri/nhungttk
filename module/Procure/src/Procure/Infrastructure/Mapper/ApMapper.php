<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocRowSnapshot;
use Procure\Domain\APInvoice\APDocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param APDocSnapshot $snapshot
     * @param FinVendorInvoice $entity
     * @return NULL|\Application\Entity\FinVendorInvoice
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, APDocSnapshot $snapshot, FinVendorInvoice $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param APDocRowSnapshot $snapshot
     * @param FinVendorInvoiceRow $entity
     * @return NULL|\Application\Entity\FinVendorInvoiceRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, APDocRowSnapshot $snapshot, FinVendorInvoiceRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param FinVendorInvoice $entity
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function createDetailSnapshot(FinVendorInvoice $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocSnapshot();

        return $snapshot;
    }

    /**
     *
     * @param FinVendorInvoiceRow $entity
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function createRowDetailSnapshot(FinVendorInvoiceRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new APDocSnapshot();

        return $snapshot;
    }
}
