<?php
namespace Procure\Application\Service\PO;

use Application\Domain\Shared\Number\NumberParser;
use Doctrine\ORM\EntityManager;
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

        // parse Number
        if (NumberParser::parseAndConvertToEN($snapshot->getDocQuantity(), $locale) == false) {
            $snapshot->addError(\sprintf('Can not parse doc quantity [%s] Locale: %s', $snapshot->getDocQuantity(), $locale));
        }

        if (NumberParser::parseAndConvertToEN($snapshot->getDocUnitPrice(), $locale) == false) {
            $snapshot->addError(\sprintf('Can not parse unit price [%s].  Locale: %s', $snapshot->getDocUnitPrice(), $locale));
        }

        if (NumberParser::parseAndConvertToEN($snapshot->getStandardConvertFactor(), $locale) == false) {
            $snapshot->addError(\sprintf('Can not parse conversion factor [%s] Locale: %s', $snapshot->getStandardConvertFactor(), $locale));
        }

        if ($snapshot->hasErrors()) {
            throw new \InvalidArgumentException($snapshot->getErrorMessage(false));
        }

        $snapshot->docQuantity = NumberParser::parseAndConvertToEN($snapshot->getDocQuantity(), $locale);
        $snapshot->docUnitPrice = NumberParser::parseAndConvertToEN($snapshot->getDocUnitPrice(), $locale);
        $snapshot->standardConvertFactor = NumberParser::parseAndConvertToEN($snapshot->getStandardConvertFactor(), $locale);
        $snapshot->conversionFactor = NumberParser::parseAndConvertToEN($snapshot->getStandardConvertFactor(), $locale);

        return $snapshot;
    }
}
