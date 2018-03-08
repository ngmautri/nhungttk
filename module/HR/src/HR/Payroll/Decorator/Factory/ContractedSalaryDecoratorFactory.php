<?php
namespace HR\Payroll\Decorator\Factory;

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
    protected function createDecorator(IncomeInterface $incomeComponent, $ytd)
    {
        return new ContractedSalaryDecorator($incomeComponent);
    }

}