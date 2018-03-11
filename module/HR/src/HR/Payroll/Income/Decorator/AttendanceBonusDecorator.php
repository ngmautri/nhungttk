<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttendanceBonusDecorator extends AbstractIncomeDecorator
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {    
        $payrollInput = $this->getConsolidatedPayrollInput();
        $employee = $payrollInput->getEmployee();        
        $percentage = 1;       
        if($employee->getStatus()=="LC"){
            return 350000*$percentage;
        }
   
        return $this->getIncomeComponent()->getAmount() * $percentage;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        $des=$this->incomeComponent->getDescription() ."\n";
        $des= $des."Calculation: based on unapproved and approved leaves.";
        
        return $des;
    }

 
}