<?php

namespace HR\Payroll\Income;
use HR\Payroll\PaymentFrequency;


/**
 * Generic Income Component
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ContractedSalary extends AbstractIncomeComponent{
    
    private $incomeName;
    private $isPITPayable;
    private $isSSOPayable;
    private $isPayable;
    private $currency;
    private $amount;
    private $calculatedAmount;
    
    private $decoratorFactory;
    
  
   /**
    * 
    * @param unknown $incomeName
    * @param unknown $amount
    * @param unknown $calculatedAmount
    * @param unknown $currency
    * @param unknown $isPITPayable
    * @param unknown $isSSOPayable
    * @param unknown $isPayable
    * @param unknown $decoratorFactory
    */
    function __construct($incomeName=null, $amount, $calculatedAmount=null,$currency,
        $isPITPayable,$isSSOPayable,$isPayable,$decoratorFactory=null)
    {
        $this->incomeName = $incomeName;
        $this->amount = $amount;
        $this->calculatedAmount = $amount;
        $this->currency = $currency;
        $this->isPayable = $isPayable;
        $this->isPITPayable = $isPITPayable;
        $this->isSSOPayable = $isSSOPayable;
        $this->decoratorFactory=$decoratorFactory;        
    }
    
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\AbstractIncomeComponent::isPITPayable()
    */
    public function isPITPayable()
    {
        return $this->isPITPayable;
    }

   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
    */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }
    
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Income\AbstractIncomeComponent::isSSOPayable()
    */
    public function isSSOPayable()
    {
        return $this->isSSOPayable;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {
        return $this->incomeName;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isPayable()
     */
    public function isPayable()
    {
        return $this->isPayable;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getAmount()
     */
    public function getAmount()
    {
        return $this->amount;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::getIncomeDecoratorFactory()
     */
    public function getIncomeDecoratorFactory()
    {
        return $this->decoratorFactory;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCurrency()
     */
    public function getCurrency()
    {
        
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::getPaymentFrequency()
     */
    public function getPaymentFrequency()
    {
        return PaymentFrequency::MONTHLY;
    }




}


