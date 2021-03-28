<?php
namespace Application\Infrastructure\Persistence\Application\Doctrine;

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
class DepartmentReportRepositoryImpl extends AbstractDoctrineRepository implements TrxReportRepositoryInterface
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
            return null;
        }

        $resultList = [];
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtInventoryMv $doctrineRootEntity ;*/
            $doctrineRootEntity = $r[0];

            $rootSnapshot = TrxMapper::createSnapshot($this->doctrineEM, $doctrineRootEntity);

            if ($rootSnapshot == null) {
                continue;
            }

            $rootSnapshot->totalRows = $r["total_row"];
            $resultList[] = $rootSnapshot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\TrxReportRepositoryInterface::getListTotal()
     */
    public function getListTotal(SqlFilterInterface $filter)
    {
        $results = TrxReportHelper::getList($this->getDoctrineEM(), $filter, null, null, null, null);
        return count($results);
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
            return null;
        }

        if ($results == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\FinVendorInvoiceRow $entity ;*/
            $entity = $r;

            $snapShot = TrxMapper::createRowSnapshot($this->getDoctrineEM(), $entity);

            if ($snapShot == null) {
                continue;
            }
            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    public function getAllRowTotal(SqlFilterInterface $filter)
    {
        $results = TrxReportHelper::getAllRow($this->getDoctrineEM(), $filter, null, null, null, null);
        return count($results);
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