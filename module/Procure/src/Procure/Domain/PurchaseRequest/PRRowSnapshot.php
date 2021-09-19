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
    public $instance;

    public $prId;

    public $prQuantity;

    public $qoQuantity;

    public $standardQoQuantity;

    public $postedQoQuantity;

    public $postedStandardQoQuantity;

    public $draftPoQuantity;

    public $standardPoQuantity;

    public $postedPoQuantity;

    public $postedStandardPoQuantity;

    public $draftGrQuantity;

    public $standardGrQuantity;

    public $postedGrQuantity;

    public $postedStandardGrQuantity;

    public $draftStockQrQuantity;

    public $standardStockQrQuantity;

    public $postedStockQrQuantity;

    public $postedStandardStockQrQuantity;

    public $draftApQuantity;

    public $standardApQuantity;

    public $postedApQuantity;

    public $postedStandardApQuantity;

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

    public $variantId;

    public $project;

    /*
     * |=============================
     * |Setter Getter
     * |
     * |=============================
     */

    /**
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
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
    public function getPrQuantity()
    {
        return $this->prQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getQoQuantity()
    {
        return $this->qoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardQoQuantity()
    {
        return $this->standardQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedQoQuantity()
    {
        return $this->postedQoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStandardQoQuantity()
    {
        return $this->postedStandardQoQuantity;
    }

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
    public function getStandardPoQuantity()
    {
        return $this->standardPoQuantity;
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
    public function getPostedStandardPoQuantity()
    {
        return $this->postedStandardPoQuantity;
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
    public function getStandardGrQuantity()
    {
        return $this->standardGrQuantity;
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
    public function getPostedStandardGrQuantity()
    {
        return $this->postedStandardGrQuantity;
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
    public function getStandardStockQrQuantity()
    {
        return $this->standardStockQrQuantity;
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
    public function getPostedStandardStockQrQuantity()
    {
        return $this->postedStandardStockQrQuantity;
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
    public function getStandardApQuantity()
    {
        return $this->standardApQuantity;
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
    public function getPostedStandardApQuantity()
    {
        return $this->postedStandardApQuantity;
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
     * @return mixed
     */
    public function getVariantId()
    {
        return $this->variantId;
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
     * @param mixed $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
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
     * @param mixed $qoQuantity
     */
    public function setQoQuantity($qoQuantity)
    {
        $this->qoQuantity = $qoQuantity;
    }

    /**
     *
     * @param mixed $standardQoQuantity
     */
    public function setStandardQoQuantity($standardQoQuantity)
    {
        $this->standardQoQuantity = $standardQoQuantity;
    }

    /**
     *
     * @param mixed $postedQoQuantity
     */
    public function setPostedQoQuantity($postedQoQuantity)
    {
        $this->postedQoQuantity = $postedQoQuantity;
    }

    /**
     *
     * @param mixed $postedStandardQoQuantity
     */
    public function setPostedStandardQoQuantity($postedStandardQoQuantity)
    {
        $this->postedStandardQoQuantity = $postedStandardQoQuantity;
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
     * @param mixed $standardPoQuantity
     */
    public function setStandardPoQuantity($standardPoQuantity)
    {
        $this->standardPoQuantity = $standardPoQuantity;
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
     * @param mixed $postedStandardPoQuantity
     */
    public function setPostedStandardPoQuantity($postedStandardPoQuantity)
    {
        $this->postedStandardPoQuantity = $postedStandardPoQuantity;
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
     * @param mixed $standardGrQuantity
     */
    public function setStandardGrQuantity($standardGrQuantity)
    {
        $this->standardGrQuantity = $standardGrQuantity;
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
     * @param mixed $postedStandardGrQuantity
     */
    public function setPostedStandardGrQuantity($postedStandardGrQuantity)
    {
        $this->postedStandardGrQuantity = $postedStandardGrQuantity;
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
     * @param mixed $standardStockQrQuantity
     */
    public function setStandardStockQrQuantity($standardStockQrQuantity)
    {
        $this->standardStockQrQuantity = $standardStockQrQuantity;
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
     * @param mixed $postedStandardStockQrQuantity
     */
    public function setPostedStandardStockQrQuantity($postedStandardStockQrQuantity)
    {
        $this->postedStandardStockQrQuantity = $postedStandardStockQrQuantity;
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
     * @param mixed $standardApQuantity
     */
    public function setStandardApQuantity($standardApQuantity)
    {
        $this->standardApQuantity = $standardApQuantity;
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
     * @param mixed $postedStandardApQuantity
     */
    public function setPostedStandardApQuantity($postedStandardApQuantity)
    {
        $this->postedStandardApQuantity = $postedStandardApQuantity;
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

    /**
     *
     * @param mixed $variantId
     */
    public function setVariantId($variantId)
    {
        $this->variantId = $variantId;
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