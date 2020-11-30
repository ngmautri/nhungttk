<?php
namespace Procure\Application\Service\PR;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Share\Snapshot\GenericRowSnapshotModifier;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PRRowSnapshotModifier
{

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function modify(PRRowSnapshot $snapshot, EntityManager $doctrineEM, $locale = null)
    {
        if (! $snapshot instanceof PRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        GenericRowSnapshotModifier::updateItemDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdateQuantity($snapshot, $doctrineEM, $locale);

        return $snapshot;
    }
}
