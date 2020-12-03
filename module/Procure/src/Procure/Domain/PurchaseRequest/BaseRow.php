<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseRow extends GenericRow
{

    // Specific Attribute
    // ===================
    protected $checksum;

    protected $priority;

    protected $rowName;

    protected $rowDescription;

    protected $rowCode;

    protected $rowUnit;

    protected $conversionText;

    protected $edt;

    protected $convertedStandardQuantity;

    protected $project;

    // ===================

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
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }
}
