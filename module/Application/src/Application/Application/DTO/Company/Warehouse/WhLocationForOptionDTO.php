<?php
namespace Application\Application\DTO\Company\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class WhLocationForOptionDTO
{

    public $whLocationShowName;

    public $whLocationName;

    public $whLocationCode;

    /**
     *
     * @return mixed
     */
    public function getWhLocationShowName()
    {
        return $this->whLocationShowName;
    }

    /**
     *
     * @param mixed $whLocationShowName
     */
    public function setWhLocationShowName($whLocationShowName)
    {
        $this->whLocationShowName = $whLocationShowName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhLocationName()
    {
        return $this->whLocationName;
    }

    /**
     *
     * @param mixed $whLocationName
     */
    public function setWhLocationName($whLocationName)
    {
        $this->whLocationName = $whLocationName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhLocationCode()
    {
        return $this->whLocationCode;
    }

    /**
     *
     * @param mixed $whLocationCode
     */
    public function setWhLocationCode($whLocationCode)
    {
        $this->whLocationCode = $whLocationCode;
    }
}
