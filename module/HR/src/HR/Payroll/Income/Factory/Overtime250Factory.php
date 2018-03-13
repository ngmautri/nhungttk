<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\PaymentFrequency;
use HR\Payroll\Income\Decorator\Factory\FullPaymentDecoratorFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Class Overtime250Factory extends AbstractIncomeFactory
{
    
    /**
     *
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {
        $incomeComponent = new GenericIncomeComponent("Overtime 250%", $amount, 0, $currency, TRUE, FALSE, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::ONE_TIME);
        $f = FullPaymentDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("Overtime from 8:00-16:00 on offical holiday, weekly restday (article 114 Labor Law)");
        return $incomeComponent;
    }
    
}