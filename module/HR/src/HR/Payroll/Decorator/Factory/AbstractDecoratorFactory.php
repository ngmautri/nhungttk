<?php
namespace HR\Payroll\Decorator\Factory;

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
    public function createIncomeDecorator(IncomeInterface $incomeComponent, $ytd){
        $decorator =  $this->createDecortor($incomeComponent, $ytd);
        return $decorator;
    }
  
    
    /**
     * 
     * @param IncomeInterface $incomeComponent
     * @param unknown $ytd
     */
    protected abstract function createDecorator(IncomeInterface $incomeComponent, $ytd);
    
}