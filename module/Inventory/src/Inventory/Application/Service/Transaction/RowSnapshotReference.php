<?php
namespace Inventory\Application\Service\Transaction;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Transaction\TrxRowSnapshot;
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
     * @param TrxRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function updateReferrence(TrxRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof TrxRowSnapshot || ! $doctrineEM instanceof EntityManager) {
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

        if ($snapshot->getItem() > 0) {
            $entity = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->getItem());

            if ($entity !== null) {
                if ($entity->getIsFixedAsset() == 1) {
                    $snapshot->isFixedAsset = 1;
                }

                if ($entity->getIsStocked() == 1) {
                    $snapshot->isInventoryItem = 1;
                }
            }
        }

        return $snapshot;
    }
}
