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
Class HousingAllowanceFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Housing Allowance", $amount, 0, $currency, TRUE, False, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::YEARLY);
        $f = FullPaymentDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Housing Allowance!");        
        return $incomeComponent;
    }

}