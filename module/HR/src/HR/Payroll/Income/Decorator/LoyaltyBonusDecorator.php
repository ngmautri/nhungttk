<?php
namespace HR\Payroll\Income\Decorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoyaltyBonusDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        $payrollInput = $this->getConsolidatedPayrollInput();
        $employee= $payrollInput->getEmployee();
        
        $working_year = $payrollInput->getEndDate()->diff($employee->getStartWorkingDate());
        $working_year=$working_year->days/365;
         
        $bonus = 0;
        if($working_year >=1 && $working_year<3){
            $bonus= 50000;
        }elseif($working_year>= 3 &&  $working_year<5){
            $bonus= 100000;
        }elseif($working_year>=5){
            $bonus= 150000;
        }
        
        return $bonus;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        $des=$this->incomeComponent->getDescription() ."\n";
        $des= $des. "It is calculated base on hire (re-hired) date and the end date of the salary period.";
        return $des;
    }
    
}