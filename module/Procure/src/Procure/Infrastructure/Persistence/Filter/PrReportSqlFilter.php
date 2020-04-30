<?php
namespace Procure\Infrastructure\Persistence\Filter;

use Procure\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $prYear;

    public $prMonth;

    public $currentState;

    public $docStatus;

    public $balance;

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
     * @param mixed $prYear
     */
    public function setPrYear($prYear)
    {
        $this->prYear = $prYear;
    }

    /**
     *
     * @param mixed $prMonth
     */
    public function setPrMonth($prMonth)
    {
        $this->prMonth = $prMonth;
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
    public function getPrYear()
    {
        return $this->prYear;
    }

    /**
     *
     * @return mixed
     */
    public function getPrMonth()
    {
        return $this->prMonth;
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
}
