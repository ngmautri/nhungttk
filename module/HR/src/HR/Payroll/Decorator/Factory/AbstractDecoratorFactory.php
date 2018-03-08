<?php
namespace HR\Payroll\Decorator\Factory;

use HR\Payroll\ConsolidatedPayrollInput;
use HR\Payroll\IncomeInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Abstract class AbstractDecoratorFactory
{
    
    /**
     * 
     * @param IncomeInterface $incomeComponent
     * @param unknown $ytd
     * @return unknown
     */
    public function createIncomeDecorator(IncomeInterface $incomeComponent, ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd){
        $decorator =  $this->createDecorator($incomeComponent, $consolidatedPayrollInput, $ytd);
        $decorator->setIdentifer(get_class($decorator));        
        return $decorator;
    }
  
    
    /**
     * 
     * @param IncomeInterface $incomeComponent
     * @param unknown $ytd
     */
    protected abstract function createDecorator(IncomeInterface $incomeComponent, 
        ConsolidatedPayrollInput $consolidatedPayrollInput, $ytd);
    
}