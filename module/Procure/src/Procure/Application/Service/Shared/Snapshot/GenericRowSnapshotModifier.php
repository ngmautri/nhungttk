<?php
namespace Procure\Application\Service\Shared\Snapshot;

use Application\Domain\Shared\Number\NumberParser;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\GenericItemSnapshot;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Procure\Domain\RowSnapshot;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericRowSnapshotModifier
{

    /**
     *
     * @param RowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updateItemDetails(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof RowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if ($snapshot->getItem() > 0) {
            $rep = new ItemQueryRepositoryImpl($doctrineEM);
            $itemSnapshot = $rep->getItemSnapshotById($snapshot->getItem());

            if ($itemSnapshot instanceof GenericItemSnapshot) {

                switch ($itemSnapshot->getItemTypeId()) {
                    case ItemType::FIXED_ASSET_ITEM_TYPE:
                        $snapshot->isFixedAsset = 1;
                        break;

                    case ItemType::INVENTORY_ITEM_TYPE:
                        $snapshot->isInventoryItem = 1;
                        break;
                    case ItemType::SERVICE_ITEM_TYPE:
                        // throw new \InvalidArgumentException("SERVICE_ITEM_TYPE!");
                        // $snapshot->warehouse = null;
                        break;
                }

                $snapshot->itemStandardUnitName = $itemSnapshot->getStandardUnitName();
            }
        }
        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @throws \InvalidArgumentException
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function updatePRDetails(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof RowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if ($snapshot->getPrRow() > 0) {
            $rep = new PRQueryRepositoryImpl($doctrineEM);

            $prSnapshot = $rep->getHeaderSnapshotByRowId($snapshot->getPrRow());

            if ($prSnapshot instanceof PRSnapshot) {
                $snapshot->pr = $prSnapshot->getId();

                if ($snapshot->getWarehouse() != $prSnapshot->getWarehouse()) {
                    // throw new \InvalidArgumentException("Warehouse must match with Warehouse of PR!" . $prSnapshot->getWarehouse());
                    // Always use PR warehouse.
                    $snapshot->warehouse = $prSnapshot->getWarehouse();
                }
            }
        }

        return $snapshot;
    }

    public static function updatePODetails(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof APRowSnapshot || $snapshot instanceof GRRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        // updating PO Referrence.
        if ($snapshot->getPoRow() > 0) {
            $rep = new POQueryRepositoryImpl($doctrineEM);
            $snapshot->po = $rep->getHeaderIdByRowId($snapshot->getPoRow());
        }
        return $snapshot;
    }

    public static function updateGRDetails(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof APRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }
        if ($snapshot->getGrRow() > 0) {
            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $snapshot->grId = $rep->getHeaderIdByRowId($snapshot->getGrRow());
        }

        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @throws \InvalidArgumentException
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function parseAndUpdateQuantity(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof RowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        // parse Number
        $parsedDocQuantity = NumberParser::parseAndConvertToEN($snapshot->getDocQuantity(), $locale);
        if ($parsedDocQuantity == false) {
            $snapshot->addError(\sprintf('Can not parse doc quantity [%s] Locale: %s', $snapshot->getDocQuantity(), $locale));
        }

        $parsedStandardConvertFactor = NumberParser::parseAndConvertToEN($snapshot->getStandardConvertFactor(), $locale);
        if ($parsedStandardConvertFactor == false) {
            $snapshot->addError(\sprintf('Can not parse standard conversion factor [%s] Locale: %s', $snapshot->getStandardConvertFactor(), $locale));
        }

        if ($snapshot->hasErrors()) {
            throw new \InvalidArgumentException($snapshot->getErrorMessage(false));
        }

        $snapshot->docQuantity = $parsedDocQuantity;
        $snapshot->conversionFactor = $parsedStandardConvertFactor;
        $snapshot->standardConvertFactor = $parsedStandardConvertFactor;

        return $snapshot;
    }

    /**
     *
     * @param RowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @param string $locale
     * @throws \InvalidArgumentException
     * @return NULL|\Procure\Domain\RowSnapshot
     */
    public static function parseAndUpdatePrice(RowSnapshot $snapshot, EntityManager $doctrineEM, $locale = 'en_EN')
    {
        if (! $snapshot instanceof RowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        // parse Number

        $parsedDocUnitPrice = NumberParser::parseAndConvertToEN($snapshot->getDocUnitPrice(), $locale);
        if ($parsedDocUnitPrice == false) {
            $snapshot->addError(\sprintf('Can not parse unit price [%s].  Locale: %s', $snapshot->getDocUnitPrice(), $locale));
        }

        /*
         * $parsedExwUnitPrice = NumberParser::parseAndConvertToEN($snapshot->getExwUnitPrice(), $locale);
         * if ($parsedExwUnitPrice == false) {
         * $snapshot->addError(\sprintf('Can not parse Exw unit price [%s]. Locale: %s', $snapshot->getExwUnitPrice(), $locale));
         * }
         */

        if ($snapshot->hasErrors()) {
            throw new \InvalidArgumentException($snapshot->getErrorMessage(false));
        }

        $snapshot->docUnitPrice = $parsedDocUnitPrice;
        $snapshot->unitPrice = $parsedDocUnitPrice;
        $snapshot->exwUnitPrice = $parsedDocUnitPrice;

        return $snapshot;
    }
}
