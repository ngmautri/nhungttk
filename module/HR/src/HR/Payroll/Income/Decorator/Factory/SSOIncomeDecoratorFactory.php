<?php
namespace HR\Payroll\Income\Decorator\Factory;

use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Income\IncomeInterface;
use HR\Payroll\Income\Decorator\SSOIncomeDecorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SSOIncomeDecoratorFactory extends AbstractDecoratorFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory::createDecorator()
     */
    protected function createDecorator(IncomeInterface $incomeComponent, ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd)
    {
        return new SSOIncomeDecorator($incomeComponent, $consolidatedPayrollInput);
    }
}