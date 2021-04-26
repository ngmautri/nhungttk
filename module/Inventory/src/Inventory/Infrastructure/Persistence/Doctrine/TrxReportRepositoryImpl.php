<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Mapper\TrxMapper;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Helper\TrxReportHelper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxReportRepositoryImpl extends AbstractDoctrineRepository implements TrxReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = TrxReportHelper::getList($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        if ($results == null) {
            yield null;
        }

        // $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtInventoryMv $doctrineRootEntity ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = TrxMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            yield $rootSnapshot;
        }

        // return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        return TrxReportHelper::getListTotal($this->getDoctrineEM(), $filter);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getAllRow()
     */
    public function getAllRow(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $results = TrxReportHelper::getAllRow($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
        if ($results == null) {
            yield null;
        }

        if ($results == null) {
            yield null;
        }

        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $entity ;*/
            $entity = $r;

            $snapShot = TrxMapper::createRowSnapshot($this->getDoctrineEM(), $entity);

            if ($snapShot == null) {
                continue;
            }
            yield $snapShot;
        }
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        return TrxReportHelper::getAllRowTotal($this->getDoctrineEM(), $filter);
    }

    public function getOfItem($item_id, $item_token)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getBeginGrGiEnd()
     */
    public function getBeginGrGiEnd(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        return TrxReportHelper::getBeginGrGiEnd($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
    }

    public function getBeginGrGiEndTotal(SqlFilterInterface $filter)
    {
        return count(TrxReportHelper::getBeginGrGiEnd($this->getDoctrineEM(), $filter, null, null, null, null));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getAllRowIssueFor()
     */
    public function getAllRowIssueFor(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        return TrxReportHelper::getCostIssueFor($this->getDoctrineEM(), $filter, $sort_by, $sort, $limit, $offset);
    }

    public function getAllRowIssueForTotal(SqlFilterInterface $filter)
    {
        return count(TrxReportHelper::getCostIssueFor($this->getDoctrineEM(), $filter, null, null, null, null));
    }
}