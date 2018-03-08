<?php
namespace HR\Payroll\Decorator\Factory;

use HR\Payroll\ConsolidatedPayrollInput;
use HR\Payroll\IncomeInterface;
use HR\Payroll\Decorator\ContractedSalaryDecorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class ContractedSalaryDecoratorFactory extends AbstractDecoratorFactory
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Decorator\Factory\AbstractDecoratorFactory::createDecorator()
     */
    protected function createDecorator(IncomeInterface $incomeComponent,
        ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd)
    {
        return new ContractedSalaryDecorator($incomeComponent,$consolidatedPayrollInput);
    }

}