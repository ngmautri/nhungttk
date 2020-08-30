<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Application\DTO\Item\Report\ItemInOutOnhandDTO;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Inventory\Infrastructure\Persistence\Contracts\ItemTrxReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Helper\ItemTrxReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemTrxReportRepositoryImpl extends AbstractDoctrineRepository implements ItemTrxReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\ItemTrxReportRepositoryInterface::getInOutOnhand()
     */
    public function getInOutOnhand(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = ItemTrxReportHelper::getInOutOnhand($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);

        if ($results == null) {
            return;
        }

        $list = [];
        foreach ($results as $r) {

            /**
             *
             * @var ItemInOutOnhandDTO $snapshot
             */
            $snapshot = ItemMapper::createSnapshot($r[0], new ItemInOutOnhandDTO());
            $snapshot->setBeginQty($r['begin_qty']);
            $snapshot->setBeginValue($r['begin_vl']);
            $snapshot->setGrQty($r['gr_qty']);
            $snapshot->setGrValue($r['gr_vl']);
            $snapshot->setGiQty($r['gi_qty']);
            $snapshot->setGiValue($r['gi_vl']);
            $snapshot->setEndQty($r['end_qty']);
            $snapshot->setEndValue($r['end_vl']);
            $list[] = $snapshot;
        }

        return $list;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\ItemTrxReportRepositoryInterface::getInOutOnhandTotal()
     */
    public function getInOutOnhandTotal(SqlFilterInterface $filter)
    {
        return count(ItemTrxReportHelper::getInOutOnhand($this->getDoctrineEM(), $filter, null, null, null, null));
    }
}