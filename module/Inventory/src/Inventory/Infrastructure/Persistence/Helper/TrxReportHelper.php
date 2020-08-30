<?php
namespace Inventory\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\BeginGrGiEndSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\CostIssueForSqlFilter;
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

        if ($filter->getDocStatus() != null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
        }

        if ($filter->getDocYear() > 0) {
            $sql = $sql . " AND YEAR(nmt_inventory_mv.posting_date)=" . $filter->getDocYear();
        }

        if ($filter->getDocMonth() > 0) {
            $sql = $sql . " AND MONTH(nmt_inventory_mv.posting_date)=" . $filter->getDocMonth();
        }

        if ($filter->getItemId() > 0) {
            $sql = $sql . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
        }

        if (! $filter->getFromDate() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_mv.posting_date >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_mv.posting_date <='%s'", $filter->getToDate());
        }

        if (! $filter->getItem() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_trx.item_id =%s", $filter->getItem());
        }

        if (! $filter->getWarehouseId() == null) {
            $sql = $sql . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
        }

        $sql = $sql . " ORDER BY nmt_inventory_trx.wh_id";
        switch ($sort_by) {
            case "vendorName":
                // $sql = $sql . " ORDER BY nmt_inventory_trx.vendor_name " . $sort;
                break;

            case "postingDate":
                $sql = $sql . ",nmt_inventory_mv.posting_date " . $sort;
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
            // $rsm->addScalarResult("posting_date", "posting_date");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getBeginGrGiEnd(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof BeginGrGiEndSqlFilter) {
            return null;
        }

        // echo $filter;

        $sql = TrxReportSQL::BEGIN_GR_GI_END;

        $sql1 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql2 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql3 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';

        if ($filter->getDocStatus() != null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql3 = $sql3 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
        }

        if ($filter->getItemId() > 0) {
            $sql1 = $sql1 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
            $sql2 = $sql2 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
            $sql3 = $sql3 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
        }

        if (! $filter->getFromDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_mv.posting_date <'%s'", $filter->getFromDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_mv.posting_date >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_mv.posting_date <='%s'", $filter->getToDate());
        }

        if (! $filter->getWarehouseId() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
            $sql3 = $sql3 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = \sprintf($sql, $sql1, $sql2, $sql3);
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("item_id", "item_id");
            $rsm->addScalarResult("wh_id", "wh_id");
            $rsm->addScalarResult("begin_qty", "begin_qty");
            $rsm->addScalarResult("gr_qty", "gr_qty");
            $rsm->addScalarResult("gi_qty", "gi_qty");
            $rsm->addScalarResult("end_qty", "end_qty");
            $rsm->addScalarResult("begin_vl", "begin_vl");
            $rsm->addScalarResult("gr_vl", "gr_vl");
            $rsm->addScalarResult("gi_vl", "gi_vl");
            $rsm->addScalarResult("end_vl", "end_vl");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getCostIssueFor(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof CostIssueForSqlFilter) {
            return null;
        }

        // echo $filter;

        $sql = TrxReportSQL::COST_ISSUE_FOR;

        $sql1 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql2 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql3 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';

        if ($filter->getDocStatus() != null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql3 = $sql3 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
        }

        if ($filter->getIssueFor() > 0) {
            $sql1 = $sql1 . " AND nmt_inventory_trx.issue_for=" . $filter->getIssueFor();
            $sql2 = $sql2 . " AND nmt_inventory_trx.issue_for=" . $filter->getIssueFor();
            $sql3 = $sql3 . " AND nmt_inventory_trx.issue_for=" . $filter->getIssueFor();
        }

        if (! $filter->getFromDate() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_mv.posting_date <'%s'", $filter->getFromDate());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_mv.posting_date >='%s'", $filter->getFromDate());
        }

        if (! $filter->getToDate() == null) {
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_mv.posting_date <='%s'", $filter->getToDate());
        }

        if (! $filter->getWarehouseId() == null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
            $sql3 = $sql3 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = \sprintf($sql, $sql1, $sql2, $sql3);
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("wh_id", "wh_id");

            $rsm->addScalarResult("item_id", "item_id");
            $rsm->addScalarResult("item_name", "item_name");
            $rsm->addScalarResult("issue_for", "issue_for");
            $rsm->addScalarResult("begin_consume_qty", "begin_consume_qty");
            $rsm->addScalarResult("begin_consume_vl", "begin_consume_vl");
            $rsm->addScalarResult("consume_qty", "consume_qty");
            $rsm->addScalarResult("consume_vl", "consume_vl");
            $rsm->addScalarResult("end_consume_qty", "end_consume_qty");
            $rsm->addScalarResult("end_consume_vl", "end_consume_vl");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
