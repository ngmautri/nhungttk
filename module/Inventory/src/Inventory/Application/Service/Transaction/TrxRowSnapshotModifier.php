<?php
namespace Inventory\Application\Service\Transaction;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Procure\Application\Service\Shared\Snapshot\GenericRowSnapshotModifier;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxRowSnapshotModifier
{

    /**
     *
     * @param TrxRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @return NULL|\Inventory\Domain\Transaction\TrxRowSnapshot
     */
    public static function modify(TrxRowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof TrxRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        GenericRowSnapshotModifier::updateItemDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updatePRDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updatePODetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updateGRDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdateQuantity($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdatePrice($snapshot, $doctrineEM, $locale);

        return $snapshot;
    }
}
