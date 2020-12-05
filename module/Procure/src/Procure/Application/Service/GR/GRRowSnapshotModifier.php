<?php
namespace Procure\Application\Service\GR;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Shared\Snapshot\GenericRowSnapshotModifier;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRRowSnapshotModifier
{

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function modify(APRowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof APRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        GenericRowSnapshotModifier::updateItemDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updatePRDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::updatePODetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdateQuantity($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdatePrice($snapshot, $doctrineEM, $locale);

        return $snapshot;
    }
}
