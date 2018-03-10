<?php
namespace HR\Payroll\Input;

use HR\Payroll\Employee;
use HR\Payroll\Exception\InvalidArgumentException;

/**
 * Consolidated Payroll Input
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPayrollInput implements PayrollInputInterface
{

    protected $employee;

    /**
     *
     * @param Employee $employee
     * @throws InvalidArgumentException
     */
    function __construct(Employee $employee)
    {
        if (! $employee instanceof Employee) {
            throw new InvalidArgumentException(sprintf('Something Wrong Component "%s" is wrong "%s".', 1, 1));
        }
        
        $this->employee = $employee;
    }

    /**
     *
     * @return \HR\Payroll\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }
}