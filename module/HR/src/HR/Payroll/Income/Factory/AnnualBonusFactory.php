<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\LoyaltyBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\TransportationAllowanceDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\FullPaymentDecoratorFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class AnnualBonusFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Annual Bonus", $amount, 0, $currency, TRUE, FALSE, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::YEARLY);
        $f = FullPaymentDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("It is also called 13th month salary!");        
        return $incomeComponent;
    }

}