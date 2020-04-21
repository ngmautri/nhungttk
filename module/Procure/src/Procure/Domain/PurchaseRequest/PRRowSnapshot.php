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
     * @return mixed
     */
    public function getDraftPoQuantity()
    {
        return $this->draftPoQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedPoQuantity()
    {
        return $this->postedPoQuantity;
    }

    /**
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getDraftApQuantity()
    {
        return $this->draftApQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedApQuantity()
    {
        return $this->postedApQuantity;
    }

    /**
     * @return mixed
     */
    public function getDraftStockQrQuantity()
    {
        return $this->draftStockQrQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedStockQrQuantity()
    {
        return $this->postedStockQrQuantity;
    }

    /**
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return mixed
     */
    public function getRowName()
    {
        return $this->rowName;
    }

    /**
     * @return mixed
     */
    public function getRowDescription()
    {
        return $this->rowDescription;
    }

    /**
     * @return mixed
     */
    public function getRowCode()
    {
        return $this->rowCode;
    }

    /**
     * @return mixed
     */
    public function getRowUnit()
    {
        return $this->rowUnit;
    }

    /**
     * @return mixed
     */
    public function getConversionText()
    {
        return $this->conversionText;
    }

    /**
     * @return mixed
     */
    public function getEdt()
    {
        return $this->edt;
    }

    /**
     * @return mixed
     */
    public function getConvertedStandardQuantiy()
    {
        return $this->convertedStandardQuantiy;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

}