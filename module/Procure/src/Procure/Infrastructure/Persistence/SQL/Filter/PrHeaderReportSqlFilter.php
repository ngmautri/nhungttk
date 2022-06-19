<?php
namespace Procure\Infrastructure\Persistence\SQL\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderReportSqlFilter extends ProcureQuerySqlFilter
{

    public $docYear;

    public $docMonth;

    public $currentState;

    public $docStatus;

    public $balance;

    public $prId;

    public $isActive;

    public function printGetQuery()
    {
        $format = "docYear=%s&docMonth=%s&docStatus=%s&balance=%s&sortBy=%s&sort=%s&limit=%s&offset=%s&resultPerPage=%s";
        return \sprintf($format, $this->getDocYear(), $this->getDocMonth(), $this->getDocStatus(), $this->getBalance(), $this->getSortBy(), $this->getSort(), $this->getLimit(), $this->getoffset(), $this->getResultPerPage());
    }

    public function printFilter()
    {
        $format = "Year=%s | Month=%s | Status=%s |  Balance=%s | Result per page=%s";
        $t = \sprintf($format, $this->getdocYear(), $this->getdocMonth(), $this->getDocStatus(), $this->getbalance(), $this->getresultPerPage());
        $l = '<hr style="margin: 5pt 1pt 5pt 1pt;">';

        $result_msg = sprintf('<div style="color:graytext; padding-top:10pt;">%s</div>', $t);
        return $result_msg . $l;
    }

    public function __toString()
    {
        $f = "PrHeaderSqlFilter_%s_%s_%s_%s_%s";
        return \sprintf($f, $this->isActive, $this->docYear, $this->docMonth, $this->docStatus, $this->balance);
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
    public function getIsActive()
    {
        return $this->isActive;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }
}
