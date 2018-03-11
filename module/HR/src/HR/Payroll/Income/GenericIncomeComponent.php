<?php

namespace HR\Payroll\Income;



/**
 * Generic Income Component
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericIncomeComponent extends AbstractIncomeComponent{
    
    private $incomeName;
    private $isPITPayable;
    private $isSSOPayable;
    private $isPayable;
    private $currency;
    private $amount;
    private $calculatedAmount;
   
    private $paymentFrequency;
    private $decoratorFactory;
    private $description;
    
  
    /**
     * Immutable Object
     * @param unknown $incomeName
     * @param unknown $amount
     * @param unknown $calculatedAmount
     * @param unknown $currency
     * @param unknown $isPITPayable
     * @param unknown $isSSOPayable
     * @param unknown $isPayable
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
     * @return unknown
     */
    public function getDecoratorFactory()
    {
        return $this->decoratorFactory;
    }

    /**
     * @param unknown $decoratorFactory
     */
    public function setDecoratorFactory($decoratorFactory)
    {
        $this->decoratorFactory = $decoratorFactory;
    }
    /**
     * @param mixed $paymentFrequency
     */
    public function setPaymentFrequency($paymentFrequency)
    {
        $this->paymentFrequency = $paymentFrequency;
    }
    public function getPaymentFrequency()
    {
        return $this->paymentFrequency;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }



}


