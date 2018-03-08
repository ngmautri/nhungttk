<?php
namespace HR\Payroll\Decorator;

use HR\Payroll\AbstractIncomeDecorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ContractedSalaryDecorator extends AbstractIncomeDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        $payrollInput = $this->getConsolidatedPayrollInput();
        
        $c1 = $payrollInput->getTotalWorkingDays();
        $c2 = $payrollInput->getActualWorkedDays();
        $c3 = $payrollInput->getPaidSickleaves();
        return $this->getIncomeComponent()->getAmount() * ($c2 + $c3) / $c1;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\IncomeInterface::getAmount()
     */
    public function getAmount()
    {}

    public function getCurrency()
    {}
}