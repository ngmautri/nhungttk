<?php
namespace HR\Domain\Salary\Contracts;

use HR\Payroll\Input\ConsolidatedPayrollInput;
use InvalidArgumentException;

/**
 * Abstract Salary Decorator to calculate income.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractSalaryDecorator implements SalaryInterface
{

    protected $salaryComponent;

    protected $consolidatedPayrollInput;

    protected $identifer;

    function __construct(SalaryInterface $salaryComponent, ConsolidatedPayrollInput $consolidatedPayrollInput)
    {
        if (! $consolidatedPayrollInput instanceof ConsolidatedPayrollInput) {
            throw new InvalidArgumentException(sprintf('No Payroll Input provided!"%s" is wrong "%s".', 1, 1));
        }

        if (! $salaryComponent instanceof SalaryInterface) {
            throw new InvalidArgumentException(sprintf('SalaryInterface  "%s" is wrong "%s".', 1, 1));
        }

        $this->salaryComponent = $salaryComponent;
        $this->consolidatedPayrollInput = $consolidatedPayrollInput;
    }

    /**
     *
     * @return mixed
     *
     */
    public function getIdentifer()
    {
        return $this->identifer;
    }
}


