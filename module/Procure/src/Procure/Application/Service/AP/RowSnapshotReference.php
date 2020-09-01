<?php
namespace Procure\Application\Service\AP;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Procure\Domain\PurchaseRequest\PRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowSnapshotReference
{

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param EntityManager $doctrineEM
     * @return NULL|\Procure\Domain\AccountPayable\APRowSnapshot
     */
    public static function updateReferrence(APRowSnapshot $snapshot, EntityManager $doctrineEM)
    {
        if (! $snapshot instanceof APRowSnapshot || ! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if ($snapshot->getPrRow() > 0) {
            $rep = new PRQueryRepositoryImpl($doctrineEM);

            $prSnapshot = $rep->getHeaderSnapshotByRowId($snapshot->getPrRow());

            if ($prSnapshot instanceof PRSnapshot) {
                $snapshot->pr = $prSnapshot->getId();
                // Always use PR warehouse.
                $snapshot->warehouse = $prSnapshot->getWarehouse();
            }
        }

        // updating referrence.
        if ($snapshot->getPoRow() > 0) {
            $rep = new POQueryRepositoryImpl($doctrineEM);
            $snapshot->po = $rep->getHeaderIdByRowId($snapshot->getPoRow());
        }

        if ($snapshot->getGrRow() > 0) {
            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $snapshot->grId = $rep->getHeaderIdByRowId($snapshot->getGrRow());
        }

        if ($snapshot->getItem() > 0) {
            $rep = new ItemQueryRepositoryImpl($doctrineEM);
            $itemSnapshot = $rep->getItemSnapshotById($snapshot->getItem());

            if ($itemSnapshot instanceof ItemSnapshot) {

                switch ($itemSnapshot->getItemTypeId()) {
                    case ItemType::FIXED_ASSET_ITEM_TYPE:
                        $snapshot->isFixedAsset = 1;
                    case ItemType::INVENTORY_ITEM_TYPE:
                        $snapshot->isInventoryItem = 1;
                }
            }
        }

        return $snapshot;
    }
}
