<?php
namespace Procure\Infrastructure\Persistence\Reporting\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoApReportSqlFilter extends AbstractProcureAppSqlFilter
{

    public $vendorId;

    public $isActive;

    public $isRowActive;

    public $fromDate;

    public $toDate;

    public $docStatus;

    public function __toString()
    {
        $f = ("PoApReportFilter_%s_%s_%s_%s_%s_%s_%s");
        $t = \sprintf($f, $this->getCompanyId(), $this->getVendorId(), $this->getIsActive(), $this->getIsRowActive(), $this->getDocStatus(), $this->getFromDate(), $this->getToDate());
        return $t;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @param mixed $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
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
    public function getDocStatus()
    {
        return $this->docStatus;
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
     * @return mixed
     */
    public function getIsRowActive()
    {
        return $this->isRowActive;
    }

    /**
     *
     * @param mixed $isRowActive
     */
    public function setIsRowActive($isRowActive)
    {
        $this->isRowActive = $isRowActive;
    }
}
