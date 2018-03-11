<?php
namespace HR\Payroll\Income\Factory;
use HR\Payroll\Income\IncomeInterface;

/**
 * Abstract Income Factory
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Abstract class AbstractIncomeFactory
{
    
    
    public function createIncomeComponent(){
        $income =  $this->createIncome();
        return $income;
    }
  
    
    /**
     * 
     * @param IncomeInterface $incomeComponent
     * @param unknown $ytd
     */
    protected abstract function createIncome();
    
}