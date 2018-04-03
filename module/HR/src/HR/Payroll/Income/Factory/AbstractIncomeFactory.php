<?php
namespace HR\Payroll\Income\Factory;
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
    * @param string $currency
    */
    function __construct($amount=0, $currency=null){
        $this->amount = $amount;
        $this->currency = $currency;
    }
    
  /**
   * 
   *  @return number
   *
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