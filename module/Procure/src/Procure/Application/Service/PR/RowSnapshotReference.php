<?php
namespace Procure\Application\Service\PR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

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
    public static function updateReferrence(PRRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof PRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        // updating referrence.
        if ($snapshot->getItem() > 0) {
            $entity = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->getItem());

            if ($entity->getIsFixedAsset() == 1) {
                $snapshot->isFixedAsset = 1;
            }

            if ($entity->getIsStocked() == 1) {
                $snapshot->isInventoryItem = 1;
            }
        }

        return $snapshot;
    }
}
