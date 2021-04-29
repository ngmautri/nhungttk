<?php
namespace Application\Application\DTO\Company\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class WarehouseForOptionDTO
{

    public $whShowName;

    public $whName;

    public $whCode;

    /**
     *
     * @return mixed
     */
    public function getWhShowName()
    {
        return $this->whShowName;
    }

    /**
     *
     * @param mixed $whShowName
     */
    public function setWhShowName($whShowName)
    {
        $this->whShowName = $whShowName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhName()
    {
        return $this->whName;
    }

    /**
     *
     * @param mixed $whName
     */
    public function setWhName($whName)
    {
        $this->whName = $whName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhCode()
    {
        return $this->whCode;
    }

    /**
     *
     * @param mixed $whCode
     */
    public function setWhCode($whCode)
    {
        $this->whCode = $whCode;
    }
}
