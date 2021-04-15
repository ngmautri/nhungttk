<?php
namespace Application\Application\DTO\Company\Department;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtApplicationCompany ;
 */
class DepartmentForOptionDTO
{

    public $departmentShowName;

    public $departmentName;

    public $departmentCode;

    /**
     *
     * @return mixed
     */
    public function getDepartmentShowName()
    {
        return $this->departmentShowName;
    }

    /**
     *
     * @param mixed $departmentShowName
     */
    public function setDepartmentShowName($departmentShowName)
    {
        $this->departmentShowName = $departmentShowName;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     *
     * @param mixed $departmentName
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
    }

    /**
     *
     * @param mixed $departmentCode
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;
    }
}
