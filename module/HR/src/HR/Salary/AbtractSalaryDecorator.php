<?php

namespace HR\Salary;

/**
 * 
 * @author nmt
 *
 */
abstract class AbtractSalaryDecorator implements IncomeInterface{
    
    protected $salaryComponent;
    
    function __construct(IncomeInterface  $salaryComponent ) {
        $this->salaryComponent = $salaryComponent;
    }
}


