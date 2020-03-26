<?php
namespace HR\Payroll\Calculator\Visitor;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
interface VisitorInterface
{

    /**
     *
     * @param AbstractVisitorElement $element
     */
    public function visit(AbstractVisitorElement $element);
}

