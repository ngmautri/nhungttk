<?php
namespace HR\Payroll\Income;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface IncomeInterface
{

    /**
     * Get the name of income component /comperator
     */
    public function getIncomeName();

    /**
     * Get amount of income.
     * Original Amount
     */
    public function getAmount();

    /**
     * Get currency of income
     */
    public function getCurrency();
    
    /**
     * PIT Payable
     */
    public function isPITPayable();

    /**
     * SSO Payble
     */
    public function isSSOPayable();

    public function isPayable();

    public function getIncomeDecoratorFactory();

    public function getPaymentFrequency();
}