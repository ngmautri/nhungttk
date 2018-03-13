<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LoadingBonusDecorator extends AbstractIncomeDecorator
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {    
        $payrollInput = $this->getConsolidatedPayrollInput();
        $n = $payrollInput->getNumberOfLoadedContainer();
   
        return $this->getIncomeComponent()->getAmount() * $n;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        $des=$this->incomeComponent->getDescription() ."\n";
        $des= $des." Calculation: rate per container * number of loaded container in month!";
        
        return $des;
    }

 
}