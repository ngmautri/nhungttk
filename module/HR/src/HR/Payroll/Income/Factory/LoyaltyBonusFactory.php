<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\LoyaltyBonusDecoratorFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class LoyaltyBonusFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Loyalty Bonus", $amount, 0, $currency, TRUE, TRUE, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f =  LoyaltyBonusDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Loyalty has been applied since 1st January 2018.");        
        return $incomeComponent;
    }

}