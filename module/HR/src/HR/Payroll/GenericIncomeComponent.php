<?php

namespace HR\Payroll;


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
        $isPITPayable,$isSSOPayable,$isPayable)
    {
        $this->incomeName = $incomeName;
        $this->amount = $amount;
        $this->calculatedAmount = $calculatedAmount;
        $this->currency = $isPayable;
        $this->isPayable = $isPITPayable;
        $this->isPITPayable = $isPITPayable;
        $this->isSSOPayable = $isSSOPayable;
        
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\AbstractIncomeComponent::isPITPayable()
     */
    public function isPITPayable()
    {
        return $this->isPITPayable;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getCurrentcy()
     */
    public function getCurrentcy()
    {
        return $this->currency;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\AbstractIncomeComponent::isSSOPayable()
     */
    public function isSSOPayable()
    {
        return $this->isSSOPayable;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {
        return $this->incomeName;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\AbstractIncomeComponent::isPayable()
     */
    public function isPayable()
    {
        return $this->isPayable;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\IncomeInterface::getAmount()
     */
    public function getAmount()
    {
        return $this->amount;
    }

}


