<?php
namespace Procure\Model\Domain\PurchaseRequest;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
Interface InterfacePurchaseRequestRepository
{
    public function getById($id);
    
    public function getByUuid($uuid);
    
    public function getAll();
}
