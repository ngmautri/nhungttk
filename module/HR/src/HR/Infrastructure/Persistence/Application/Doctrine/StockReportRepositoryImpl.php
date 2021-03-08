<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface;
use Inventory\Infrastructure\Persistence\Filter\StockFifoLayerReportSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\StockOnhandReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class StockReportRepositoryImpl extends AbstractDoctrineRepository implements StockReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface::getFifoLayer()
     */
    public function getFifoLayer(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof StockFifoLayerReportSqlFilter) {
            throw new \InvalidArgumentException("StockFifoLayerReportSqlFilter object not valid!");
        }

        $sql = "
SELECT
*
FROM nmt_inventory_fifo_layer
WHERE 1 %s";

        $sql_tmp = '';
        if ($filter->getItemId() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.item_id=%s", $filter->getItemId());
        }
        if ($filter->getWarehouseId() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.warehouse_id=%s", $filter->getWarehouseId());
        }
        if ($filter->getFromDate() != null) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.posting_date >='%s'", $filter->getFromDate());
        }

        if ($filter->getToDate() != null) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.posting_date <='%s'", $filter->getToDate());
        }

        if ($filter->getIsClosed() == null) {
            $filter->setIsClosed(0);
        }

        if ($filter->getIsClosed() == 1) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.is_closed=%s", 1);
        } elseif ($filter->getIsClosed() == 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.is_closed=%s", 0);
        }

        switch ($sort_by) {
            case "postingDate":
                $sql_tmp = $sql_tmp . " ORDER BY nmt_inventory_fifo_layer.posting_date " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp = $sql_tmp . $sql_tmp . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = \sprintf($sql, $sql_tmp);

        $sql = $sql . ";";

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryFifoLayer', 'nmt_inventory_fifo_layer');
            // $rsm->addScalarResult("current_onhand", "current_onhand");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getDetailsFifoLayer(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof StockFifoLayerReportSqlFilter) {
            throw new \InvalidArgumentException("StockFifoLayerReportSqlFilter object not valid!");
        }

        $sql = "
SELECT
nmt_inventory_fifo_layer.*
FROM nmt_inventory_fifo_layer
Left join nmt_inventory_trx
on nmt_inventory_trx.uuid = nmt_inventory_fifo_layer.source_token

WHERE 1 %s";

        $sql_tmp = '';
        if ($filter->getItemId() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.item_id=%s", $filter->getItemId());
        }
        if ($filter->getWarehouseId() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.warehouse_id=%s", $filter->getWarehouseId());
        }
        if ($filter->getFromDate() != null) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.posting_date >='%s'", $filter->getFromDate());
        }

        if ($filter->getToDate() != null) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.posting_date <='%s'", $filter->getToDate());
        }

        if ($filter->getMovementId() > 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_trx.movement_id =%s", $filter->getMovementId());
        }

        if ($filter->getIsClosed() == null) {
            $filter->setIsClosed(0);
        }

        if ($filter->getIsClosed() == 1) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.is_closed=%s", 1);
        } elseif ($filter->getIsClosed() == 0) {
            $sql_tmp = $sql_tmp . \sprintf(" AND nmt_inventory_fifo_layer.is_closed=%s", 0);
        }

        switch ($sort_by) {
            case "postingDate":
                $sql_tmp = $sql_tmp . " ORDER BY nmt_inventory_fifo_layer.posting_date " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql_tmp = $sql_tmp . $sql_tmp . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = \sprintf($sql, $sql_tmp);

        $sql = $sql . ";";

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryFifoLayer', 'nmt_inventory_fifo_layer');
            // $rsm->addScalarResult("movement_id", "movement_id");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface::getOnHandQuantity()
     */
    public function getOnHandQuantity(SqlFilterInterface $filter)
    {
        if (! $filter instanceof StockOnhandReportSqlFilter) {
            throw new \InvalidArgumentException("OnhandReportSqlFilter object not valid!");
        }

        $sql = "
SELECT
     SUM(nmt_inventory_fifo_layer.onhand_quantity) AS current_onhand
FROM nmt_inventory_fifo_layer
WHERE 1 %s
GROUP BY nmt_inventory_fifo_layer.item_id, nmt_inventory_fifo_layer.warehouse_id
";

        $sql1 = sprintf("AND nmt_inventory_fifo_layer.posting_date <='%s'
AND nmt_inventory_fifo_layer.item_id = %s
AND nmt_inventory_fifo_layer.warehouse_id = %s
AND nmt_inventory_fifo_layer.is_closed=0", $filter->getCheckingDate(), $filter->getItemId(), $filter->getWarehouseId());

        $sql = sprintf($sql, $sql1);

        $sql = $sql . ";";
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryFifoLayer', 'nmt_inventory_fifo_layer');
            $rsm->addScalarResult("current_onhand", "current_onhand");

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return (int) $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface::getOnHandQuantityAtLocation()
     */
    public function getTrxOnHandQuantity(SqlFilterInterface $filter)
    {
        if (! $filter instanceof StockOnhandReportSqlFilter) {
            throw new \InvalidArgumentException("OnhandReportSqlFilter object not valid!");
        }

        $sql = "
SELECT
    nmt_inventory_trx.item_id,
    nmt_inventory_trx.wh_id,
    nmt_inventory_trx.wh_location,
    SUM(CASE WHEN nmt_inventory_trx.is_active =1  AND nmt_inventory_trx.flow = 'IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS in_qty,
    SUM(CASE WHEN nmt_inventory_trx.is_active =1  AND nmt_inventory_trx.flow = 'OUT' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS out_qty
FROM nmt_inventory_trx
Left join nmt_inventory_mv
On nmt_inventory_mv.id = nmt_inventory_trx.movement_id
WHERE 1 %s
GROUP BY nmt_inventory_trx.item_id, nmt_inventory_trx.wh_id, nmt_inventory_trx.wh_location


";

        $f = "AND nmt_inventory_mv.movement_date <='%s' AND nmt_inventory_trx.item_id = %s";
        $sql1 = sprintf($f, $filter->getCheckingDate(), $filter->getItemId());

        $sql = sprintf($sql, $sql1);

        $sql = $sql . ";";
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("total_gr_qty", "total_gr_qty");
            $rsm->addScalarResult("total_gi_qty", "total_gi_qty");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return (int) $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}