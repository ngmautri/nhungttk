<?php
namespace Procure\Application\Service\PO;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Shared\Snapshot\GenericRowSnapshotModifier;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PORowSnapshotModifier
{

    /**
     *
     * @param PORowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public static function modify(PORowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
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
