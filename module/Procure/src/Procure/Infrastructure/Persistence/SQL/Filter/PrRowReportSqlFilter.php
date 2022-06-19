<?php
namespace Procure\Infrastructure\Persistence\SQL\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowReportSqlFilter extends ProcureQuerySqlFilter
{

    public $docYear;

    public $docMonth;

    public $currentState;

    public $docStatus;

    public $balance;

    public $itemId;

    public $prId;

    public $rowId;

    public $isActive;

    public function resetLimitOffset()
    {
        $this->setLimit(0);
        $this->setOffset(0);
    }

    public function printGetQueryForPR()
    {
        $format = "balance=%s&sortBy=%s&sort=%s&limit=%s&offset=%s&resultPerPage=%s";
        return \sprintf($format, $this->getBalance(), $this->getSortBy(), $this->getSort(), $this->getLimit(), $this->getoffset(), $this->getResultPerPage());
    }

    public function printGetQueryForPRReport()
    {
        $format = "docYear=%s&docMonth=%s&currentState=%s&docStatus=%s&balance=%s&itemId=%s&prId=%s&rowId=%s&isActive=%s&companyId=%s&sortBy=%s&sort=%s&limit=%s&offset=%s&resultPerPage=%s";
        return \sprintf($format, $this->getdocYear(), $this->getdocMonth(), $this->getcurrentState(), $this->getdocStatus(), $this->getbalance(), $this->getitemId(), $this->getprId(), $this->getrowId(), $this->getisActive(), $this->getcompanyId(), $this->getsortBy(), $this->getsort(), $this->getlimit(), $this->getoffset(), $this->getresultPerPage());
    }

    public function __toString()
    {
        // $f = \sprintf("PrRowSqlFilter_%s_%s_%s_%s_%s_%s_%s");
        // return \sprintf($f, $this->prId, $this->isActive, $this->docYear, $this->docMonth, $this->docStatus, $this->balance, $this->itemId);
        $f = "PrRowSqlFilter_%s_%s_%s_%s_%s_%s";
        return \sprintf($f, $this->prId, $this->isActive, $this->docYear, $this->docMonth, $this->docStatus, $this->balance, $this->itemId);
    }

    /**
     *
     * @return mixed
     */
    public function getPrId()
    {
        return $this->prId;
    }

    /**
     *
     * @return mixed
     */
    public function getRowId()
    {
        return $this->rowId;
    }

    /**
     *
     * @param mixed $prId
     */
    public function setPrId($prId)
    {
        $this->prId = $prId;
    }

    /**
     *
     * @param mixed $rowId
     */
    public function setRowId($rowId)
    {
        $this->rowId = $rowId;
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
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
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
