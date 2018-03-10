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
        return parent::getAmount();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCurrency()
     */
    public function getCurrency()
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {}
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getAmount()
     */
    public function getAmount()
    {}


}