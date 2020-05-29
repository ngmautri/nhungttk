<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Entity\NmtInventoryItem;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Inventory\Infrastructure\Persistence\Contracts\ItemReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\ItemSerialSqlFilter;
use Inventory\Infrastructure\Persistence\Helper\ItemReportHelper;

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
        $results = ItemReportHelper::getItemList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        var_dump(count($results));
        if ($results == null) {
            return;
        }

        $list = [];
        foreach ($results as $r) {
            $snapshot = ItemMapper::createSnapshot($r);
            $list[] = $snapshot;
        }

        return $list;
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
}