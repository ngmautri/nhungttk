<?php
namespace Procure\Infrastructure\Mapper;

use Application\Entity\FinVendorInvoice;
use Application\Entity\FinVendorInvoiceRow;
use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocRowSnapshot;
use Procure\Domain\APInvoice\APDocSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Application\Entity\NmtProcurePr;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Application\Entity\NmtProcurePrRow;
use Procure\Domain\PurchaseRequest\PRRowDetailsSnapshot;
use Procure\Domain\PurchaseRequest\PRDetailsSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrMapper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PRSnapshot $snapshot
     * @param NmtProcurePr $entity
     * @return NULL|\Application\Entity\NmtProcurePr
     */
    public static function mapSnapshotEntity(EntityManager $doctrineEM, PRSnapshot $snapshot, NmtProcurePr $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param PRRowSnapshot $snapshot
     * @param NmtProcurePrRow $entity
     * @return NULL|\Application\Entity\NmtProcurePrRow
     */
    public static function mapRowSnapshotEntity(EntityManager $doctrineEM, PRRowSnapshot $snapshot, NmtProcurePrRow $entity)
    {
        if ($snapshot == null || $entity == null || $doctrineEM == null) {
            return null;
        }

        return $entity;
    }

    /**
     *
     * @param NmtProcurePr $entity
     * @return NULL|\Procure\Domain\PurchaseRequest\PRDetailsSnapshot
     */
    public static function createDetailSnapshot(NmtProcurePr $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PRDetailsSnapshot();

        return $snapshot;
    }

    /**
     *
     * @param NmtProcurePrRow $entity
     * @return NULL|\Procure\Domain\PurchaseRequest\PRRowDetailsSnapshot
     */
    public static function createRowDetailSnapshot(NmtProcurePrRow $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new PRRowDetailsSnapshot();

        return $snapshot;
    }
}
