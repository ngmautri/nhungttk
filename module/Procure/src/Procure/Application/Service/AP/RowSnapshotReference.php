<?php
namespace Procure\Application\Service\AP;

use Doctrine\ORM\EntityManager;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowSnapshotReference
{

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function updateReferrence(APRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof APRowSnapshot || ! $doctrineEM instanceof EntityManager) {
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

        if ($snapshot->getGrRow() > 0) {
            $apQuery = new GRQueryRepositoryImpl($doctrineEM);
            // $snapshot-> = $apQuery->getHeaderIdByRowId($snapshot->getApInvoiceRow());
        }

        return $snapshot;
    }
}
