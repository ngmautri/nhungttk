<?php
namespace Inventory\Service\Report;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use PDO;

/**
 * Warehouse Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportService extends AbstractService
{

    const WH_ITEM_ONHAND_SQL = "
SELECT
	nmt_inventory_trx.wh_id,
	SUM(CASE WHEN (nmt_inventory_trx.flow = 'IN') THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gr,
	SUM(CASE WHEN (nmt_inventory_trx.flow = 'OUT') THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gi,
	(SUM(CASE WHEN (nmt_inventory_trx.flow = 'IN') THEN (nmt_inventory_trx.quantity) ELSE 0 END)-SUM(CASE WHEN (nmt_inventory_trx.flow = 'OUT') THEN (nmt_inventory_trx.quantity) ELSE 0 END)) AS current_balance
FROM nmt_inventory_trx
WHERE 1 and %s
GROUP BY nmt_inventory_trx.item_id, nmt_inventory_trx.wh_id
";

    /**
     *
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     * @param string $trigger
     */
    public function getOnhandReportByItem($item, $u, $trigger = null)
    {
        if ($item == null or $u == null) {
            return null;
        }
        $sql1 = sprintf('item_id=%s AND nmt_inventory_trx.is_active =1', $item->getId());
        $sql = sprintf(self::WH_ITEM_ONHAND_SQL, $sql1);

        $stmt = $this->getDoctrineEM()
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $entity
     * @param \Application\Entity\NmtInventoryItem $item
     *
     *
     * @param \Application\Entity\MlaUsers $u
     * @param string $trigger
     */
    public function getOnhandInWahrehouse($entity, $item, $u, $trigger = null)
    {
        $sql = "
SELECT
     SUM(nmt_inventory_fifo_layer.onhand_quantity) AS current_onhand
FROM nmt_inventory_fifo_layer
WHERE 1 %s
GROUP BY nmt_inventory_fifo_layer.item_id, nmt_inventory_fifo_layer.warehouse_id
";

        if ($item == null or $u == null) {
            return null;
        }

        $sql1 = sprintf("AND nmt_inventory_fifo_layer.posting_date <='%s' 
AND nmt_inventory_fifo_layer.item_id = %s 
AND nmt_inventory_fifo_layer.warehouse_id = %s 
AND nmt_inventory_fifo_layer.is_closed=0", $entity->getTrxDate()->format('Y-m-d H:i:s'), $item->getId(), $entity->getWh()->getId());

        $sql = sprintf($sql, $sql1);
        $stmt = $this->getDoctrineEM()
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        if (count($results) == 1) {
            return $results[0]['current_onhand'];
        }
        return 0;
    }

    /**
     *
     * @param int $warehouse_id
     * @param int $item_id
     * @param int $is_active
     * @param int $is_posted
     */
    public function getItemsInWarehouse($warehouse_id, $item_id, $is_active, $is_posted)
    {
        $sql = "SELECT
        nmt_inventory_trx.item_id,
        nmt_inventory_item.item_name,
        nmt_inventory_trx.wh_id,
        nmt_inventory_trx.wh_location,
        SUM(CASE WHEN (nmt_inventory_trx.flow = 'IN') THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS in_amount,
        SUM(CASE WHEN (nmt_inventory_trx.flow = 'OUT') THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS out_amount
        FROM nmt_inventory_trx
        JOIN nmt_inventory_warehouse
        ON nmt_inventory_trx.wh_id= nmt_inventory_warehouse.id
        
        LEFT JOIN nmt_inventory_item
        ON nmt_inventory_trx.item_id = nmt_inventory_item.id
        
        WHERE 1 %s
        GROUP BY nmt_inventory_trx.item_id,  nmt_inventory_trx.wh_id, nmt_inventory_trx.wh_location";
    }
}
