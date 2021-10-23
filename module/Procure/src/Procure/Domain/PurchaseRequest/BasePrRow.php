<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class BasePrRow extends GenericRow
{

    /*
     * |=============================
     * |Specific Fields on DB
     * |
     * |=============================
     */
    protected $checksum;

    protected $priority;

    protected $rowName;

    protected $rowDescription;

    protected $rowCode;

    protected $rowUnit;

    protected $conversionText;

    protected $edt;

    protected $pr;

    /*
     * |=============================
     * | Setter and Getter
     * |
     * |=============================
     */
    /**
     *
     * @param mixed $checksum
     */
    protected function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $priority
     */
    protected function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     *
     * @param mixed $rowName
     */
    protected function setRowName($rowName)
    {
        $this->rowName = $rowName;
    }

    /**
     *
     * @param mixed $rowDescription
     */
    protected function setRowDescription($rowDescription)
    {
        $this->rowDescription = $rowDescription;
    }

    /**
     *
     * @param mixed $rowCode
     */
    protected function setRowCode($rowCode)
    {
        $this->rowCode = $rowCode;
    }

    /**
     *
     * @param mixed $rowUnit
     */
    protected function setRowUnit($rowUnit)
    {
        $this->rowUnit = $rowUnit;
    }

    /**
     *
     * @param mixed $conversionText
     */
    protected function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
    }

    /**
     *
     * @param mixed $edt
     */
    protected function setEdt($edt)
    {
        $this->edt = $edt;
    }

    /**
     *
     * @param mixed $pr
     */
    protected function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     *
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     *
     * @return mixed
     */
    public function getRowName()
    {
        return $this->rowName;
    }

    /**
     *
     * @return mixed
     */
    public function getRowDescription()
    {
        return $this->rowDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getRowCode()
    {
        return $this->rowCode;
    }

    /**
     *
     * @return mixed
     */
    public function getRowUnit()
    {
        return $this->rowUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionText()
    {
        return $this->conversionText;
    }

    /**
     *
     * @return mixed
     */
    public function getEdt()
    {
        return $this->edt;
    }

    /**
     *
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }
}
