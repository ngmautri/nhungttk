<?php
namespace HR\Payroll\Decorator;
use HR\Payroll\AbstractIncomeDecorator;

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
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
    }
    
    public function getIncomeName()
    {}

    public function getAmount()
    {}
    public function getCurrency()
    {}



}