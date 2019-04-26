<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
Interface InterfacePurchaseRequestRepository
{
    public function get($id);
    
    public function getAll();
}
