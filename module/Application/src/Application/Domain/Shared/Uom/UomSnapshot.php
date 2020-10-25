<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomSnapshot extends AbstractDTO
{

    public $id;

    public $uomCode;

    public $uomName;

    public $symbol;

    public $alias;

    public $uomDescription;

    public $conversionFactor;

    public $sector;

    public $status;

    public $createdOn;

    public $createdBy;

    public $company;

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
    public function getSymbol()
    {
        return $this->symbol;
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
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     *
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     *
     * @param mixed $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
}
