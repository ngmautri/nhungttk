<?php

namespace HR\Payroll;

/**
 * Abstract Income Componente
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractIncomeComponent implements IncomeInterface{
   
    abstract public function isPITPayable();
    
    abstract public function isSSOPayable();
    
    abstract public function isPayable();
}


