<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SSOIncomeDecorator extends AbstractIncomeDecorator
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {    
         return $this->getAmount()*5.5/100;
    }
    /**
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        // TODO Auto-generated method stub
        return "SSO Employee Contribution: 5.5%";
    }

    
    
 
}