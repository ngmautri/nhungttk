<?php
namespace HR\Payroll\Decorator;
use HR\Payroll\AbstractIncomeDecorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttendanceBonusDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        //base on unapproved leave, approved leave
   
        parent::getAmount()*23/26;
    }
    
    public function getCurrentcy()
    {}

    public function getIncomeName()
    {}

    public function getAmount()
    {}


}