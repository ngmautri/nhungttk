<?php
namespace Procure\Model\DomainDriven\Repository\Doctrine;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
Interface InterfaceQuotationOrderRepository
{
    public function get($id);
    
    public function getAll();
}
