<?php
namespace Procure\Application\Service\QR;

use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Shared\Snapshot\GenericRowSnapshotModifier;
use Procure\Domain\QuotationRequest\QRRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QRRowSnapshotModifier
{

    /**
     *
     * @param QRRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @return NULL|\Procure\Domain\QuotationRequest\QRRowSnapshot
     */
    public static function modify(QRRowSnapshot $snapshot, EntityManager $doctrineEM, $locale = null)
    {
        if (! $snapshot instanceof QRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        GenericRowSnapshotModifier::updateItemDetails($snapshot, $doctrineEM, $locale);
        GenericRowSnapshotModifier::parseAndUpdateQuantity($snapshot, $doctrineEM, $locale);

        return $snapshot;
    }
}
