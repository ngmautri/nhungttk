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
     * @return mixed
     */
    public function getPrQuantity()
    {
        return $this->prQuantity;
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
     * @return mixed
     */
    public function getLastVendorName()
    {
        return $this->lastVendorName;
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
     * @return mixed
     */
    public function getLastStandardUnitPrice()
    {
        return $this->lastStandardUnitPrice;
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
     * @return mixed
     */
    public function getLastCurrency()
    {
        return $this->lastCurrency;
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
     * @param mixed $prId
     */
    public function setPrId($prId)
    {
        $this->prId = $prId;
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
     * @param mixed $lastVendorId
     */
    public function setLastVendorId($lastVendorId)
    {
        $this->lastVendorId = $lastVendorId;
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
     * @param mixed $lastUnitPrice
     */
    public function setLastUnitPrice($lastUnitPrice)
    {
        $this->lastUnitPrice = $lastUnitPrice;
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
     * @param mixed $lastStandardConvertFactor
     */
    public function setLastStandardConvertFactor($lastStandardConvertFactor)
    {
        $this->lastStandardConvertFactor = $lastStandardConvertFactor;
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
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
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
     * @param mixed $rowName
     */
    public function setRowName($rowName)
    {
        $this->rowName = $rowName;
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
     * @param mixed $rowCode
     */
    public function setRowCode($rowCode)
    {
        $this->rowCode = $rowCode;
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
     * @param mixed $conversionText
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
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