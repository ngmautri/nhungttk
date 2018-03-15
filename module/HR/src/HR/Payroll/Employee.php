<?php
namespace HR\Payroll;

use HR\Payroll\Income\IncomeInterface;

/**
 * Employee
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class Employee
{
    private $employeeCode;
    private $employeeName;
    private $basicSalary;
    
    private $status;
    private $startWorkingDate;
    
    /**
     * @return mixed
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
    }

    

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

   

    /**
     * @param mixed $employeeCode
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    /**
     * @return mixed
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @return mixed
     */
    public function getStartWorkingDate()
    {
        return $this->startWorkingDate;
    }

    /**
     * @param mixed $employeeName
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
    }

    /**
     * @param mixed $startWorkingDate
     */
    public function setStartWorkingDate($startWorkingDate)
    {
        $this->startWorkingDate = $startWorkingDate;
    }
    /**
     * @return mixed
     */
    public function getBasicSalary()
    {
        return $this->basicSalary;
    }

    /**
     * 
     * @param IncomeInterface $basicSalary
     */
    public function setBasicSalary(IncomeInterface $basicSalary)
    {
        $this->basicSalary = $basicSalary;
    }



   
}