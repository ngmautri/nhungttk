<?php
namespace Inventory\Application\Specification\Doctrine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnhandQuantityAtLocationSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $wareHouseId = null;
        if (isset($subject["warehouseId"])) {
            $wareHouseId = $subject["warehouseId"];
        }

        $itemId = null;
        if (isset($subject["itemId"])) {
            $itemId = $subject["itemId"];
        }

        $movementDate = null;
        if (isset($subject["movementDate"])) {
            $movementDate = $subject["movementDate"];
        }

        $docQuantity = null;
        if (isset($subject["docQuantity"])) {
            $docQuantity = $subject["docQuantity"];
        }

        if ($this->doctrineEM == null || $itemId == null || $wareHouseId == null || $movementDate == null || $docQuantity == null) {
            return false;
        }

        $onhand = 0;

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
AND nmt_inventory_fifo_layer.is_closed=0", $movementDate, $itemId, $wareHouseId);

        $sql = sprintf($sql, $sql1);
        $stmt = $this->getDoctrineEM()
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        if (count($results) == 1)
            $onhand = $results[0]['current_onhand'];

        if ($onhand < $docQuantity || $onhand == 0)
            return false;

        return true;
    }
}