<?php
namespace Inventory\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\TrxReportSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter;
use Inventory\Infrastructure\Persistence\SQL\TrxReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxReportHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof TrxReportSqlFilter) {
            return null;
        }

        $sql_filtered = '';

        if ($filter->getDocStatus() == "all") {
            $filter->docStatus = null;
        }

        if ($filter->getDocYear() == "all") {
            $filter->docYear = null;
        }

        if ($filter->getIsActive() == 1) {
            $sql_filtered = $sql_filtered . " AND nmt_inventory_mv.is_active = 1";
        } elseif ($filter->getIsActive() == - 1) {
            $sql_filtered = $sql_filtered . " AND nmt_inventory_mv.is_active = 0";
        }

        if ($filter->getDocStatus() != null) {
            $sql_filtered = $sql_filtered . \sprintf(' AND nmt_inventory_mv.doc_status ="%s"', $filter->getDocStatus());
        }

        if ($filter->getDocYear() != null) {
            $sql_filtered = $sql_filtered . \sprintf(' AND year(nmt_inventory_mv.doc_date) = %s', $filter->getDocYear());
        }

        if ($filter->getDocMonth() != null) {
            $sql_filtered = $sql_filtered . \sprintf(' AND month(nmt_inventory_mv.doc_date) = %s', $filter->getDocMonth());
        }

        if ($filter->getMovementType() != null) {
            $sql_filtered = $sql_filtered . \sprintf(' AND nmt_inventory_mv.movement_type = "%s"', $filter->getMovementType());
        }

        // SQL;
        $sql = TrxReportSQL::TRX_LIST;
        $sql = $sql . \sprintf("%s GROUP BY nmt_inventory_mv.id", $sql_filtered);

        switch ($sort_by) {
            case "sysNumber":
                $sql = $sql . " ORDER BY nmt_inventory_mv.sys_number " . $sort;
                break;
            case "docDate":
                $sql = $sql . " ORDER BY nmt_inventory_mv.doc_date " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_inventory_mv.created_on " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryMv', 'nmt_inventory_mv');
            $rsm->addScalarResult("total_row", "total_row");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|object
     */
    static public function getAllRow(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof TrxRowReportSqlFilter) {
            return null;
        }

        // echo $filter;

        $sql = TrxReportSQL::TRX_ROWS;

        $sql_tmp = '';

        if ($filter->getDocYear() > 0) {
            $sql = $sql . " AND YEAR(nmt_inventory_mv.posting_date)=" . $filter->getDocYear();
        }

        if ($filter->getDocMonth() > 0) {
            $sql = $sql . " AND MONTH(nmt_inventory_mv.posting_date)=" . $filter->getDocMonth();
        }

        if ($filter->getItem() > 0) {
            $sql = $sql . " AND nmt_inventory_trx.item_id=" . $filter->getItem();
        }

        if (! $filter->getFromDate() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_mv.posting_date >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_mv.posting_date <='%s'", $filter->getToDate());
        }

        switch ($sort_by) {
            case "vendorName":
                // $sql = $sql . " ORDER BY nmt_inventory_trx.vendor_name " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
