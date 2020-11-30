<?php
namespace Procure\Application\Service\PO;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Share\Snapshot\GenericRowSnapshotModifier;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RowSnapshotModifier
{

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function updateFrom(PORowSnapshot $snapshot, EntityManager $doctrineEM, $locale = null)
    {
        if (! $snapshot instanceof PORowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }
        GenericRowSnapshotModifier::updateItemDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updatePRDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdateQuantity($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdatePrice($snapshot, $doctrineEM, $locale);
        return $snapshot;
    }
}
