<?php
namespace HR\Payroll\Income\Decorator\Factory;

use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Income\IncomeInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDecoratorFactory
{

    /**
     *
     * @param IncomeInterface $incomeComponent
     * @param ConsolidatedPayrollInput $consolidatedPayrollInput
     * @param int $ytd
     * @return mixed
     *
     */
    public function createIncomeDecorator(IncomeInterface $incomeComponent, ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd)
    {
        $decorator = $this->createDecorator($incomeComponent, $consolidatedPayrollInput, $ytd);
        $decorator->setIdentifer(get_class($decorator));
        return $decorator;
    }

    /**
     *
     * @param IncomeInterface $incomeComponent
     * @param int $ytd
     */
    protected abstract function createDecorator(IncomeInterface $incomeComponent, ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd);
}