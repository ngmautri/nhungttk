<?php
namespace HR\Payroll\Income\Decorator\Factory;

use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Income\IncomeInterface;
use HR\Payroll\Income\Decorator\BasicSalaryDecorator;

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
    * @see \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory::createDecorator()
    */
    protected function createDecorator(IncomeInterface $incomeComponent,
        ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd)
    {
        return new BasicSalaryDecorator($incomeComponent,$consolidatedPayrollInput);
    }

}