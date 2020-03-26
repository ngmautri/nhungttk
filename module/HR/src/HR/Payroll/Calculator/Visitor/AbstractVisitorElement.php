<?php
namespace HR\Payroll\Calculator\Visitor;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
abstract class AbstractVisitorElement
{

    /**
     *
     * @param VisitorInterface $visitor
     */
    abstract public function accept(VisitorInterface $visitor);
}

