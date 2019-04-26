<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Interface InterfacePurchaseRequest
{
    public function addRow();
    public function editRow();
    public function validateHeader();    
}
