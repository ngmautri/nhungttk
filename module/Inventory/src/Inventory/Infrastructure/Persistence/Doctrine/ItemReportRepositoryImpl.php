<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Entity\NmtInventoryItem;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Infrastructure\Doctrine\Helper\ItemCollectionHelper;
use Inventory\Infrastructure\Doctrine\Helper\ItemStatistisHelper;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\ItemSerialSqlFilter;
use Inventory\Infrastructure\Persistence\Helper\ItemReportHelper;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemReportRepositoryImpl extends AbstractDoctrineRepository implements ItemReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface::getItemList()
     */
    public function getItemList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ItemReportHelper::getItemList1($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            yield ;
        }

        // $list = [];
        foreach ($results as $r) {
            // $snapshot = ItemMapper::createSnapshot($r);
            // yield $snapshot;
            yield $r;
        }

        // return $list;
    }

    public function getItemListForIndexing(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ItemReportHelper::getItemList1($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            yield ;
        }

        // $list = [];
        foreach ($results as $rootEntityDoctrine) {

            $id = $rootEntityDoctrine->getId();
            $rootSnapshot = ItemMapper::createSnapshot($rootEntityDoctrine, null, true);
            $rootEntity = ItemFactory::contructFromDB($rootSnapshot);

            Assert::notNull($rootEntity);

            $doctrineEM = $this->getDoctrineEM();
            $rootEntity->setStatistics(ItemStatistisHelper::createItemStatistics($doctrineEM, $id));
            $rootEntity->setVariantCollectionRef(ItemCollectionHelper::createVariantCollectionRef($doctrineEM, $id));
            $rootEntity->setPictureCollectionRef(ItemCollectionHelper::createPictureCollectionRef($doctrineEM, $rootEntityDoctrine));

            yield $rootEntity;
        }
    }

    public function getItemList1(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ItemReportHelper::getItemList1($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return;
        }

        // $list = [];
        foreach ($results as $r) {
            $snapshot = ItemMapper::createSnapshot($r);
            $list[] = $snapshot;
        }

        // return $list;
    }

    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0)
    {}

    public function getLastCreatedItems($limit = 100, $offset = 0)
    {}

    public function getAllItemWithSerial($itemId = null)
    {
        $filter = new ItemSerialSqlFilter();
        $filter->setItemId($itemId);
        $sort_by = null;
        $sort = null;
        $limit = null;
        $offset = null;

        $results = ItemReportHelper::getItemListWithSerialNumber($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        echo count($results);
        $n = 0;
        foreach ($results as $r) {

            /**
             *
             * @var NmtInventoryItem $r ;
             */
            $n ++;
            echo $n . "-" . $r->getItemName() . "\n";
            $i = 0;
            foreach ($r->getSerialNoList() as $s) {
                $i ++;
                echo "    Fifo: " . $i . " : " . $s->getId() . "\n";
            }
        }
    }

    public function get($id, $token)
    {
        return ItemReportHelper::getItem($this->getDoctrineEM(), $id, $token);
    }

    public function getLastAPRows($limit = 100, $offset = 0)
    {}

    public function getOnhandInWahrehouse($itemId, $wareHouseId, $transactionDate)
    {}

    public function getRandomItem()
    {}

    public function getMostOrderItems($limit = 50, $offset = 0)
    {}

    public function getLastCreatedPrRow($limit = 100, $offset = 0)
    {}

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}

    public function getListTotal(SqlFilterInterface $filter)
    {}

    public function getItemListTotal(SqlFilterInterface $filter)
    {
        return ItemReportHelper::getItemListTotal($this->getDoctrineEM(), $filter);
    }
}