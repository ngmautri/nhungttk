<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Shared\ValueObjectInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseUom extends ValueObject implements \JsonSerializable, ValueObjectInterface
{

    protected $id;

    protected $uomCode;

    protected $uomName;

    protected $uomDescription;

    protected $conversionFactor;

    protected $sector;

    protected $symbol;

    protected $status;

    protected $createdOn;

    protected $createdBy;

    protected $company;

    /**
     *
     * @return mixed
     */
    public function getUomCode()
    {
        return $this->uomCode;
    }

    /**
     *
     * @return mixed
     */
    public function getUomName()
    {
        return $this->uomName;
    }

    /**
     *
     * @return mixed
     */
    public function getUomDescription()
    {
        return $this->uomDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     *
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @param mixed $uomCode
     */
    public function setUomCode($uomCode)
    {
        $this->uomCode = $uomCode;
    }

    /**
     *
     * @param mixed $uomName
     */
    public function setUomName($uomName)
    {
        $this->uomName = $uomName;
    }

    /**
     *
     * @param mixed $uomDescription
     */
    public function setUomDescription($uomDescription)
    {
        $this->uomDescription = $uomDescription;
    }

    /**
     *
     * @param mixed $conversionFactor
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     *
     * @param mixed $sector
     */
    public function setSector($sector)
    {
        $this->sector = $sector;
    }

    /**
     *
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     *
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}
