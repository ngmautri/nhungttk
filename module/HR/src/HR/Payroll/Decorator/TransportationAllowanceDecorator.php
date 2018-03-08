<?php
namespace HR\Payroll\Decorator;
use HR\Payroll\AbstractIncomeDecorator;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TransportationAllowanceDecorator extends AbstractIncomeDecorator
{
    /**
     * Transportation allownace is based on actual working day
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        $payrollInput = $this->getConsolidatedPayrollInput();
        
        $c1 = $payrollInput->getTotalWorkingDays();
        $c2 = $payrollInput->getActualWorkedDays();
        $c3 = $payrollInput->getPaidSickleaves();
        
        return $this->getIncomeComponent()->getAmount()*$c2/$c1;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCurrentcy()
     */
    public function getCurrency()
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getAmount()
     */
    public function getAmount()
    {}

    
}