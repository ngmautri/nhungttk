<?php
namespace HR\Payroll\Income\Decorator;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TransportationAllowanceDecorator extends AbstractIncomeDecorator
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {
        $payrollInput = $this->getConsolidatedPayrollInput();
        
        $c1 = $payrollInput->getTotalWorkingDays();
        $c2 = $payrollInput->getActualWorkedDays();
        $c3 = $payrollInput->getPaidSickleaves();
        
        return $this->getIncomeComponent()->getAmount()*$c2/$c1;
    }

   
    public function getCurrency()
    {}

    public function getIncomeName()
    {}

   
    public function getAmount()
    {}

    
}