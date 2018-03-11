<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PITIncomeDecorator extends AbstractIncomeDecorator
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {    
       return $this->getAmount()*0.05;
    }
    /**
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        // TODO Auto-generated method stub
        return "PIT 5%";
    }

    
    
 
}