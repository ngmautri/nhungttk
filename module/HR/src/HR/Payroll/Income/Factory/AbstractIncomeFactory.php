<?php
namespace HR\Payroll\Income\Factory;
use HR\Payroll\Income\IncomeInterface;

/**
 * Abstract Income Factory
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractIncomeFactory
{
    private $amount;
    private $currency;
    
   /**
    * 
    * @param number $amount
    * @param unknown $currency
    */
    function __construct($amount=0, $currency=null){
        $this->amount = $amount;
        $this->currency = $currency;
    }
    
   /**
    * 
    * @return unknown
    */
    public function createIncomeComponent(){
        $income =  $this->createIncome($this->amount, $this->currency);
        return $income;
    }
  
    
   /**
    * 
    * @param number $amount
    */
    protected abstract function createIncome($amount=0, $currency=null);
    
}