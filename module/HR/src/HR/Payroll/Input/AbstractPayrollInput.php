<?php
namespace HR\Payroll\Input;

use HR\Payroll\Employee;
use HR\Payroll\Exception\InvalidArgumentException;
use Zend\Validator\Date;

/**
 * Abstract Payroll Input
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPayrollInput implements PayrollInputInterface
{

    protected $employee;
    protected $startDate;
    protected $endDate;

  /**
   * 
   * @param Employee $employee
   * @param \DateTime $startDate : start date of salary period
   * @param \DateTime $endDate   : end date of salary period
   * @throws InvalidArgumentException
   */
    function __construct(Employee $employee, \DateTime $startDate, \DateTime $endDate)
    {
        if (! $employee instanceof Employee) {
            throw new InvalidArgumentException(sprintf('Invalid Argurment. "%s" is expected!', 'Employee Class'));
        }
        
        $validator = new Date();
        $validated = 0;
        
        if (! $validator->isValid($startDate)) {
            throw new InvalidArgumentException('Invalid Argurment! Start Date is not correct');
        } else {
            $validated ++;
        }
        
        if (! $validator->isValid($endDate)) {
            throw new InvalidArgumentException('Invalid Argurment! End Date is not correct');
        } else {
            $validated ++;
        }
        
        if ($validated == 2) {
            if ($startDate > $endDate) {
                throw new InvalidArgumentException('Invalid Argurment! End Date is earlier then Start Date.');
            }
        }
        
        $this->employee = $employee;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     *
     * @return \HR\Payroll\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     *
     * @return \\DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     *
     * @return \\DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

}