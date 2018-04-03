<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\LoyaltyBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\TransportationAllowanceDecoratorFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class ProductivityBonusFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Productivity Bonus", $amount, 0, $currency,FALSE, TRUE, False, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f = TransportationAllowanceDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Productivity bonus is applied for production.");        
        return $incomeComponent;
    }

}