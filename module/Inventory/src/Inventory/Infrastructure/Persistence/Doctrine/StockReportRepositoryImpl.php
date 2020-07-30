<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface;

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
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\Contracts\StockReportRepositoryInterface::getOnHandQuantity()
     */
    public function getOnHandQuantity($warehouseId, $itemId, $movementDate)
    {
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
AND nmt_inventory_fifo_layer.is_closed=0", $movementDate, $itemId, $warehouseId);
    }
}