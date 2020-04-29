<?php
namespace Procure\Application\Service\QR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\QuotationRequest\QRRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowSnapshotReference
{

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\QuotationRequest\QRRowSnapshot|\Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public static function updateReferrence(QRRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof QRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
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
