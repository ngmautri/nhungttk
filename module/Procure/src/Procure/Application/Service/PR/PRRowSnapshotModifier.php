<?php
namespace Procure\Application\Service\PR;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Shared\Snapshot\GenericRowSnapshotModifier;
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
     * @param PRRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @return NULL|\Procure\Domain\PurchaseRequest\PRRowSnapshot
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
