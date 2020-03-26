<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory;
use HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BasicSalaryFactory extends AbstractIncomeFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount = 0, $currency = null)
    {
        $incomeComponent = new GenericIncomeComponent("Basic Salary", $amount, 0, $currency, TRUE, TRUE, TRUE, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f = ContractedSalaryDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Basic Salary as per contract. Basic salary must be in compliance with minumum wage by law.");

        return $incomeComponent;
    }
}