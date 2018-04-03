<?php
namespace HR\Payroll\Calculator\Visitor;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Abstract Class AbstractVisitorElement
{
    /**
     * 
     *  @param VisitorInterface $visitor
     */
    abstract public function accept(VisitorInterface $visitor);
}

