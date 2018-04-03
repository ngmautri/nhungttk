<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class AttendanceBonusFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Attendance Bonus", $amount, 0, $currency, False, TRUE, False, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f =  AttendanceBonusDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Attendance bonus is aim to encourage employee to follow the company rule");
        return $incomeComponent;
    }

}