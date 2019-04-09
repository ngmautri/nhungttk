<?php
namespace Procure\Model\DomainDriven\Repository\Doctrine;

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
