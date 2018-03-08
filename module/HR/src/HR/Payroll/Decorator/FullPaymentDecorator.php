<?php
namespace HR\Payroll\Decorator;
use HR\Payroll\AbstractIncomeDecorator;

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
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        //return unmodified amount   
        return parent::getAmount();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCurrency()
     */
    public function getCurrency()
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {}
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getAmount()
     */
    public function getAmount()
    {}


}