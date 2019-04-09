<?php
namespace Procure\Model\DomainDriven\Entity;

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
