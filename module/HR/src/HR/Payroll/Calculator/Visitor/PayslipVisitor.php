<?php
namespace HR\Payroll\Calculator\Visitor;

use HR\Payroll\Payroll;
use HR\Payroll\Exception\InvalidArgumentException;

/**
 * Payslip Vistor to print Payslip
 * @author Nguyen Mau Tri
 *        
 */
Class PayslipVisitor implements VisitorInterface
{
   /**
    * 
    * {@inheritDoc}
    * @see \HR\Payroll\Calculator\Visitor\VisitorInterface::visit()
    */
    public function visit(AbstractVisitorElement $element)
    {
        if(!$element instanceof Payroll){
                 throw new InvalidArgumentException("Invalid argurment! I expect a Payroll");
        }
        
        // Lset make paysslip
        echo $element->getEmployee()->getEmployeeCode() . "accepted me. thanks";
        
        
    }

   
}

