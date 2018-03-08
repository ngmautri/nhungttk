<?php
namespace HR\Payroll;


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
     * Get amount of income
     */
    public function getAmount();
    
    /**
     * Get currency of income
     */
    public function getCurrentcy();
    
    /**
     * Get amount of income
     */
    public function getCalculatedAmount();
       
  
}