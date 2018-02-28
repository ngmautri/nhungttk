<?php
namespace HR\Salary;


Interface IncomeInterface
{
    /**
     * Get the name of income component
     */
    public function getIncomeName();
    
    
    /**
     * Get amount of income
     */
    public function getAmount();
    
    /**
     * Is the income subject to PIT
     */
    public function isPITPayable();
    
   /**
    * Is the income subject to Social Security
    */
    public function isSSOPayable();
    
    
    /**
     * It is to check, if the income to paybale together with the monthly salary
     * Some staff get Housing allowance, but this was paid in advance separatly.
     */
    public function isPayble();
}