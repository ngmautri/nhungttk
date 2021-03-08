<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $docYear;

    public $docMonth;

    public $movementType;

    public $docStatus;

    public $fromDate;

    public $toDate;

    public $flow;

    public function __toString()
    {
        return \sprintf("TrxReportSqlFilter_%s_%s_%s_%s_%s_%s_%s_%s", $this->isActive, $this->docYear, $this->docMonth, $this->movementType, $this->docStatus, $this->fromDate, $this->toDate, $this->flow);
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
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     *
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $docYear
     */
    public function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @param mixed $docMonth
     */
    public function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
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
}
