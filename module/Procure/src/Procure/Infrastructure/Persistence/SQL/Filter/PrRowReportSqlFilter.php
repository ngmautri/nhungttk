<?php
namespace Procure\Infrastructure\Persistence\SQL\Filter;

use Procure\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $docYear;

    public $docMonth;

    public $currentState;

    public $docStatus;

    public $balance;

    public $itemId;

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
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function __toString()
    {
        return \sprintf("PrReportSqlFilter_%s_%s_%s_%s_%s_%s", $this->isActive, $this->docYear, $this->docMonth, $this->docStatus, $this->balance, $this->itemId);
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
    public function getCurrentState()
    {
        return $this->currentState;
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
    public function getBalance()
    {
        return $this->balance;
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
     * @param mixed $currentState
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
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
     * @param mixed $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }
}
