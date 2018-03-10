<?php
namespace HR\Payroll;

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
    
    private $status;
    private $startDate;
    
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
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
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    
   
  
}