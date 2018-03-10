<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OvertimeDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        return null;
    }
    
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
    */
    public function getDescription(){
        return "Overtime = overtime*rate/26";
    }
    
    
    public function getIncomeName()
    {}

    public function getAmount()
    {}
    public function getCurrency()
    {}



}