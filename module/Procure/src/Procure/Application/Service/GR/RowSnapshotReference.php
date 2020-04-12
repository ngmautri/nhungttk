<?php
namespace Procure\Application\Service\GR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

class RowSnapshotReference
{

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    public static function updateReferrence(GRRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof GRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        // updating referrence.
        if ($snapshot->getPoRow() > 0) {
            $poQuery = new POQueryRepositoryImpl($doctrineEM);
            $snapshot->po = $poQuery->getHeaderIdByRowId($snapshot->getPoRow());
        }

        if ($snapshot->getPrRow() > 0) {
            $poQuery = new PRQueryRepositoryImpl($doctrineEM);
            $snapshot->pr = $poQuery->getHeaderIdByRowId($snapshot->getPrRow());
        }

        if ($snapshot->getApInvoiceRow() > 0) {
            $apQuery = new APQueryRepositoryImpl($doctrineEM);
            $snapshot->invoice = $apQuery->getHeaderIdByRowId($snapshot->getApInvoiceRow());
        }
        
       
        return $snapshot;
    }
}
