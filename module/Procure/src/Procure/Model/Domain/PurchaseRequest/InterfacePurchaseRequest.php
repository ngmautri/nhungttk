<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
Interface InterfacePurchaseRequest
{
    public function addRow(InterfacePurchaseRequestRow $row);
    public function editRow(InterfacePurchaseRequestRow $row);
    public function submit();
    public function validateHeader();    
}
