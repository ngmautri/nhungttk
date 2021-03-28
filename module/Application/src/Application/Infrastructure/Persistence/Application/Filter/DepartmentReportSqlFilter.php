<?php
namespace Application\Infrastructure\Persistence\Application\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $movementType;

    public $docStatus;

    public $fromDate;

    public $toDate;

    public $flow;

    public $item;

    public $itemId;

    public $warehouseId;

    public function __toString()
    {
        $f = "TrxRowReportSqlFilter_I%s_WH%s_FD%s_TD%s_A%s_S%s_F%s";
        return \sprintf($f, $this->getItemId(), $this->getWarehouseId(), $this->getFromDate(), $this->getToDate(), $this->getIsActive(), $this->getDocStatus(), $this->getFlow());
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getMovementType()
    {
        return $this->movementType;
    }

    /**
     *
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

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
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $movementType
     */
    public function setMovementType($movementType)
    {
        $this->movementType = $movementType;
    }

    /**
     *
     * @param mixed $docStatus
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
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
     * @param mixed $flow
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;
    }

    /**
     *
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
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
}
