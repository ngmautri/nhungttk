<?php

namespace HR\Payroll;


/*
 * 
 */
class ContractedSalary implements IncomeInterface{
    
    private $amount;
    
    
    /**
    * 
    * {@inheritDoc}
    * @see \HR\Salary\IncomeInterface::isPITPayable()
    */ 
    public function isPITPayable()
    {return true;}

    /**
     * 
     * {@inheritDoc}
     * @see \HR\Salary\IncomeInterface::isSSOPayable()
     */
    public function isSSOPayable()
    { return true;}
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Salary\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {
        return "Contracted Salary";   
    }

    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Salary\IncomeInterface::getAmount()
     */
    public function getAmount()
    {
        
        return $this->amount;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Salary\IncomeInterface::isPayble()
     */
    public function isPayble()
    {return true;}
    public function getCalculatedAmount()
    {}

  
}


