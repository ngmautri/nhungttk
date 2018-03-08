<?php
namespace HR\Payroll\Decorator\Factory;

use HR\Payroll\ConsolidatedPayrollInput;
use HR\Payroll\IncomeInterface;
use HR\Payroll\Decorator\FullPaymentDecorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class FullPaymentDecoratorFactory extends AbstractDecoratorFactory
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Decorator\Factory\AbstractDecoratorFactory::createDecorator()
     */
    protected function createDecorator(IncomeInterface $incomeComponent,
        ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd)
    {
        return new FullPaymentDecorator($incomeComponent,$consolidatedPayrollInput);
    }

}