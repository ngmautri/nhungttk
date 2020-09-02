<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
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
        return ItemTrxReportHelper::getInOutOnhand($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
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