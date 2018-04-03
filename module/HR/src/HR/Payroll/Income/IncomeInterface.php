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
     * Original Amount
     */
    public function getAmount();

    /**
     * Get currency of income
     */
    public function getCurrency();
    
    /**
     * Default Component is assigned to any contract.
     */
    public function isDefault();
    
    /**
     * PIT Payable
     */
    public function isPITPayable();

    /**
     * SSO Payble
     */
    public function isSSOPayable();
    
    /**
     * Pay or not pay
     */
    public function isPayable();

    /**
     * Decorator, Income calculation
     */
    public function getIncomeDecoratorFactory();

    /**
     * Payment Frequency
     */
    public function getPaymentFrequency();
    
    /**
     * Payment Description
     */
    public function getDescription();
}