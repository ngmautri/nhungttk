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

            /**
             *
             * @var \Application\Entity\NmtInventoryItem $entity ;
             */
            $entity = $doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($snapshot->getItem());

            if ($entity->getIsFixedAsset() == 1) {
                $snapshot->isFixedAsset = 1;
            }

            if ($entity->getIsStocked() == 1) {
                $snapshot->isInventoryItem = 1;
            }

            $stardardUom=$entity->getStandardUom();
            if($stardardUom!=null){
                $snapshot->itemStandardUnitName = $stardardUom->getUomName();
            }

        }

        // parse Number

        $parsedDocQuantity = NumberParser::parseAndConvertToEN($snapshot->getDocQuantity(), $locale);
        if ($parsedDocQuantity == false) {
            $snapshot->addError(\sprintf('Can not parse doc quantity [%s] Locale: %s', $snapshot->getDocQuantity(), $locale));
        }

        $parsedDocUnitPrice = NumberParser::parseAndConvertToEN($snapshot->getDocUnitPrice(), $locale);
        if ($parsedDocUnitPrice == false) {
            $snapshot->addError(\sprintf('Can not parse unit price [%s].  Locale: %s', $snapshot->getDocUnitPrice(), $locale));
        }

        $parsedExwUnitPrice= NumberParser::parseAndConvertToEN($snapshot->getExwUnitPrice(), $locale);
        if ($parsedExwUnitPrice == false) {
            $snapshot->addError(\sprintf('Can not parse Exw unit price [%s].  Locale: %s', $snapshot->getExwUnitPrice(), $locale));
        }

        $parsedStandardConvertFactor = NumberParser::parseAndConvertToEN($snapshot->getStandardConvertFactor(), $locale);
        if ($parsedStandardConvertFactor == false) {
            $snapshot->addError(\sprintf('Can not parse conversion factor [%s] Locale: %s', $snapshot->getStandardConvertFactor(), $locale));
        }


        if ($snapshot->hasErrors()) {
            throw new \InvalidArgumentException($snapshot->getErrorMessage(false));
        }

        $snapshot->docQuantity = $parsedDocQuantity;
        $snapshot->docUnitPrice = $parsedDocUnitPrice;
        $snapshot->exwUnitPrice = $parsedExwUnitPrice;
        $snapshot->standardConvertFactor = $parsedStandardConvertFactor;
        $snapshot->conversionFactor = $parsedStandardConvertFactor;

        return $snapshot;
    }
}
