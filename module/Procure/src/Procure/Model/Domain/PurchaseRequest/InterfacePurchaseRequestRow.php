<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Interface InterfacePurchaseRequestRow
{
    public function add();
    public function edit();
    public function validate();    
}
