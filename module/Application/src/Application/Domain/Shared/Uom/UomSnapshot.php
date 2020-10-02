<?php
namespace Application\Domain\Shared\Uom;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class UomSnapshot
{

    private $uomCode;

    private $uomName;

    private $symbol;
    /**
     * @return mixed
     */
    public function getUomCode()
    {
        return $this->uomCode;
    }

    /**
     * @return mixed
     */
    public function getUomName()
    {
        return $this->uomName;
    }

    /**
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param mixed $uomCode
     */
    public function setUomCode($uomCode)
    {
        $this->uomCode = $uomCode;
    }

    /**
     * @param mixed $uomName
     */
    public function setUomName($uomName)
    {
        $this->uomName = $uomName;
    }

    /**
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }


  }
