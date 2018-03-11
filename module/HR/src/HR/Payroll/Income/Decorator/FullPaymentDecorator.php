<?php
namespace HR\Payroll\Income\Decorator;

/**
 * Full Payment decorator
 * no deduction.
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FullPaymentDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        //return unmodified amount   
        return $this->getAmount();
    }
    
    /**
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        // TODO Auto-generated method stub
        $des="No modification!";
        return $des;
    }

   
}