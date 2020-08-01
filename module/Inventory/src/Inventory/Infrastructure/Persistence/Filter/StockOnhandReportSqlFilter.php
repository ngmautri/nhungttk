<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class StockOnhandReportSqlFilter implements SqlFilterInterface
{

    public $checkingDate;

    public $itemId;

    public $warehouseId;

    /**
     *
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
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
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function __toString()
    {
        $f = "OnhandReportSqlFilter_%s_%s_%s";
        return \sprintf($f, $this->warehouseId, $this->itemId, $this->checkingDate);
    }

    /**
     *
     * @return mixed
     */
    public function getCheckingDate()
    {
        return $this->checkingDate;
    }

    /**
     *
     * @param mixed $checkingDate
     */
    public function setCheckingDate($checkingDate)
    {
        $this->checkingDate = $checkingDate;
    }

    /**
     *
     * @param mixed $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }
}
