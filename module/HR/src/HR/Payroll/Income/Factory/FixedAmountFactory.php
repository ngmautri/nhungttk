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
Class FixedAmountFactory extends AbstractIncomeFactory
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\Factory\AbstractIncomeFactory::createIncome()
     */
    protected function createIncome($amount=0, $currency=null)
    {        
        $incomeComponent = new GenericIncomeComponent("Fixed Amount (Factory Rate)", $amount, 0, $currency,FALSE, TRUE, FALSE, TRUE);
        $incomeComponent->setPaymentFrequency(PaymentFrequency::MONTHLY);
        $f =  ContractedSalaryDecoratorFactory::class;
        $incomeComponent->setDecoratorFactory($f);
        $incomeComponent->setDescription("It is a part of basis salary, that is changed upon annual salary adjustment!");
        
        return $incomeComponent;
    }

}