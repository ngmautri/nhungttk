<?php
namespace HR\Payroll\Decorator;
use HR\Payroll\AbstractIncomeDecorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ContractedSalaryDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        parent::getAmount()*23/26;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCurrentcy()
     */
    public function getCurrentcy()
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