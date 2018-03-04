<?php

namespace HR\Payroll;

/**
 * 
 * @author nmt
 *
 */
abstract class AbstractIncomeDecorator implements IncomeInterface{
    
    protected $incomeComponent;
    
    /**
     * 
     * @param IncomeInterface $incomeComponent
     */
    function __construct(IncomeInterface  $incomeComponent ) {
        $this->incomeComponent = $incomeComponent;
    }
}


