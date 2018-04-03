<?php

namespace HR\Payroll\Income;

/**
 * Abstract Income Component
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Abstract class AbstractIncomeComponent implements IncomeInterface{
    
    private $incomeName;
    private $isPITPayable;
    private $isSSOPayable;
    private $isPayable;
    
    private $currency;
    private $amount;
    private $calculatedAmount;    
    
    private $decoratorFactory;
    private $paymentFrequency;
    
    
    /**
     * 
     *  @param string $incomeName
     *  @param number $amount
     *  @param number $calculatedAmount
     *  @param unknown $currency
     *  @param unknown $isPITPayable
     *  @param unknown $isSSOPayable
     *  @param unknown $isPayable
     *  @param unknown $decoratorFactory
     */
    function __construct($incomeName=null, $amount=0, $calculatedAmount=0,$currency,
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
 
}


