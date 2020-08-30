<?php
namespace Inventory\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\InOutOnhandSqlFilter;
use Inventory\Infrastructure\Persistence\SQL\ItemTrxReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemTrxReportHelper
{

    static public function getInOutOnhand(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof InOutOnhandSqlFilter) {
            return null;
        }

        // echo $filter;

        $sql = ItemTrxReportSQL::IN_OUT_ONHAND;

        $sql1 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql2 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql3 = ' AND nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.wh_location IS NULL';
        $sql4 = ' ';

        if ($filter->getDocStatus() != null) {
            $sql1 = $sql1 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql2 = $sql2 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
            $sql3 = $sql3 . \sprintf(" AND nmt_inventory_trx.doc_status ='%s'", $filter->getDocStatus());
        }

        if ($filter->getItemId() > 0) {
            $sql1 = $sql1 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
            $sql2 = $sql2 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
            $sql3 = $sql3 . " AND nmt_inventory_trx.item_id=" . $filter->getItemId();
            $sql4 = $sql4 . " AND nmt_inventory_item.id=" . $filter->getItemId();
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
            $sql4 = $sql4 . \sprintf(" AND nmt_inventory_trx.wh_id =%s", $filter->getWarehouseId());
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = \sprintf($sql, $sql1, $sql2, $sql3, $sql4);
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
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
}
