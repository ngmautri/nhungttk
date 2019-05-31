<?php
namespace Inventory\Application\Specification\Doctrine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnhandQuantitySpecification extends DoctrineSpecification
{

    private $warehouseId;

    private $transactionDate;

    private $issueQuantity;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $itemId = $subject;
        $wareHouseId = $this->warehouseId;
        $transactionDate = $this->transactionDate;
        $issueQuantity = $this->issueQuantity;

        if ($this->doctrineEM == null || $itemId == null or $wareHouseId == null || $transactionDate == null || $issueQuantity == null)
            return false;

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
AND nmt_inventory_fifo_layer.is_closed=0", $transactionDate, $itemId, $wareHouseId);

        $sql = sprintf($sql, $sql1);
        $stmt = $this->getDoctrineEM()
            ->getConnection()
            ->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        if (count($results) == 1)
            $onhand = $results[0]['current_onhand'];

        if ($onhand < $issueQuantity || $onhand == 0)
            return false;

        return true;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     *
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     *
     * @param mixed $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }
}