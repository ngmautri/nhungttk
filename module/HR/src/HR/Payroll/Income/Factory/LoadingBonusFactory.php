<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\LoyaltyBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\TransportationAllowanceDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\FullPaymentDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\LoadingBonusDecoratorFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LoadingBonusFactory extends AbstractIncomeFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount = 0, $currency = null)
    {
        $incomeComponent = new GenericIncomeComponent("Loading Bonus", $amount, 0, $currency, FALSE, TRUE, False, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f = LoadingBonusDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Loading Bonus is only applied for worker to load garment for export.");
        return $incomeComponent;
    }
}