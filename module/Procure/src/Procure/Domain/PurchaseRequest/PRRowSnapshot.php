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

    public $draftPoQuantity;

    public $postedPoQuantity;

    public $draftGrQuantity;

    public $postedGrQuantity;

    public $draftApQuantity;

    public $postedApQuantity;

    public $draftStockQrQuantity;

    public $postedStockQrQuantity;

    public $lastVendorId;

    public $lastVendorName;

    public $lastUnitPrice;

    public $lastCurrency;

    public $checksum;

    public $priority;

    public $rowName;

    public $rowDescription;

    public $rowCode;

    public $rowUnit;

    public $conversionText;

    public $edt;

    public $convertedStandardQuantiy;

    public $project;

    /**
     *
     * @return mixed
     */
    public function getDraftPoQuantity()
    {
        return $this->draftPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedPoQuantity()
    {
        return $this->postedPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftApQuantity()
    {
        return $this->draftApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedApQuantity()
    {
        return $this->postedApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockQrQuantity()
    {
        return $this->draftStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockQrQuantity()
    {
        return $this->postedStockQrQuantity;
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
     * @return mixed
     */
    public function getConvertedStandardQuantiy()
    {
        return $this->convertedStandardQuantiy;
    }

    /**
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @param mixed $draftPoQuantity
     */
    public function setDraftPoQuantity($draftPoQuantity)
    {
        $this->draftPoQuantity = $draftPoQuantity;
    }

    /**
     *
     * @param mixed $postedPoQuantity
     */
    public function setPostedPoQuantity($postedPoQuantity)
    {
        $this->postedPoQuantity = $postedPoQuantity;
    }

    /**
     *
     * @param mixed $draftGrQuantity
     */
    public function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     *
     * @param mixed $postedGrQuantity
     */
    public function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     *
     * @param mixed $draftApQuantity
     */
    public function setDraftApQuantity($draftApQuantity)
    {
        $this->draftApQuantity = $draftApQuantity;
    }

    /**
     *
     * @param mixed $postedApQuantity
     */
    public function setPostedApQuantity($postedApQuantity)
    {
        $this->postedApQuantity = $postedApQuantity;
    }

    /**
     *
     * @param mixed $draftStockQrQuantity
     */
    public function setDraftStockQrQuantity($draftStockQrQuantity)
    {
        $this->draftStockQrQuantity = $draftStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStockQrQuantity
     */
    public function setPostedStockQrQuantity($postedStockQrQuantity)
    {
        $this->postedStockQrQuantity = $postedStockQrQuantity;
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

    /**
     *
     * @param mixed $convertedStandardQuantiy
     */
    public function setConvertedStandardQuantiy($convertedStandardQuantiy)
    {
        $this->convertedStandardQuantiy = $convertedStandardQuantiy;
    }

    /**
     *
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }
}