<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class StockFifoLayerReportSqlFilter implements SqlFilterInterface
{

    public $itemId;

    public $warehouseId;

    public $isClosed;

    public $fromDate;

    public $toDate;

    public $movementId;

    /**
     *
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     *
     * @return mixed
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     *
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     *
     * @param mixed $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     *
     * @return mixed
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     *
     * @param mixed $isClosed
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
    }

    public function __toString()
    {
        $f = "StockFifoLayerReportSqlFilter_%s_%s_%s";
        return \sprintf($f, $this->warehouseId, $this->itemId, $this->isClosed);
    }

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
     * @return mixed
     */
    public function getMovementId()
    {
        return $this->movementId;
    }

    /**
     *
     * @param mixed $movementId
     */
    public function setMovementId($movementId)
    {
        $this->movementId = $movementId;
    }
}
