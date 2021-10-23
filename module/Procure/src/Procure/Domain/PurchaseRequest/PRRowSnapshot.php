<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRowSnapshot extends RowSnapshot
{

    /*
     * |=============================
     * | Procure\Domain\PurchaseRequest\PRRow
     * |
     * |=============================
     */
    public $prId;

    public $prQuantity;

    public $lastVendorId;

    public $lastVendorName;

    public $lastUnitPrice;

    public $lastStandardUnitPrice;

    public $lastStandardConvertFactor;

    public $lastCurrency;

    /*
     * |=============================
     * | Procure\Domain\PurchaseRequest\BasePrRow
     * |
     * |=============================
     */
    public $checksum;

    public $priority;

    public $rowName;

    public $rowDescription;

    public $rowCode;

    public $rowUnit;

    public $conversionText;

    public $edt;

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
     * @param mixed $prId
     */
    public function setPrId($prId)
    {
        $this->prId = $prId;
    }

    /**
     *
     * @return mixed
     */
    public function getPrQuantity()
    {
        return $this->prQuantity;
    }

    /**
     *
     * @param mixed $prQuantity
     */
    public function setPrQuantity($prQuantity)
    {
        $this->prQuantity = $prQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getLastVendorId()
    {
        return $this->lastVendorId;
    }

    /**
     *
     * @param mixed $lastVendorId
     */
    public function setLastVendorId($lastVendorId)
    {
        $this->lastVendorId = $lastVendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getLastVendorName()
    {
        return $this->lastVendorName;
    }

    /**
     *
     * @param mixed $lastVendorName
     */
    public function setLastVendorName($lastVendorName)
    {
        $this->lastVendorName = $lastVendorName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUnitPrice()
    {
        return $this->lastUnitPrice;
    }

    /**
     *
     * @param mixed $lastUnitPrice
     */
    public function setLastUnitPrice($lastUnitPrice)
    {
        $this->lastUnitPrice = $lastUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastStandardUnitPrice()
    {
        return $this->lastStandardUnitPrice;
    }

    /**
     *
     * @param mixed $lastStandardUnitPrice
     */
    public function setLastStandardUnitPrice($lastStandardUnitPrice)
    {
        $this->lastStandardUnitPrice = $lastStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastStandardConvertFactor()
    {
        return $this->lastStandardConvertFactor;
    }

    /**
     *
     * @param mixed $lastStandardConvertFactor
     */
    public function setLastStandardConvertFactor($lastStandardConvertFactor)
    {
        $this->lastStandardConvertFactor = $lastStandardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getLastCurrency()
    {
        return $this->lastCurrency;
    }

    /**
     *
     * @param mixed $lastCurrency
     */
    public function setLastCurrency($lastCurrency)
    {
        $this->lastCurrency = $lastCurrency;
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
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
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
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
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
     * @param mixed $rowName
     */
    public function setRowName($rowName)
    {
        $this->rowName = $rowName;
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
     * @param mixed $rowDescription
     */
    public function setRowDescription($rowDescription)
    {
        $this->rowDescription = $rowDescription;
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
     * @param mixed $rowCode
     */
    public function setRowCode($rowCode)
    {
        $this->rowCode = $rowCode;
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
     * @param mixed $rowUnit
     */
    public function setRowUnit($rowUnit)
    {
        $this->rowUnit = $rowUnit;
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
     * @param mixed $conversionText
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
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
     * @param mixed $edt
     */
    public function setEdt($edt)
    {
        $this->edt = $edt;
    }
}