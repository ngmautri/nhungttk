<?php
namespace Procure\Model\Domain\PurchaseRequest\Doctrine;

use Procure\Model\Domain\PurchaseRequest\InterfacePurchaseRequest;


/**
 *
 * @author Nguyen Mau Tri
 *        
 * @ORM\Entity       
 */
class DoctrinePurchaseRequest extends \Application\Entity\NmtProcurePr implements InterfacePurchaseRequest
{
    
    
    public function validateHeader()
    {}

    public function editRow()
    {}

    public function addRow()
    {}
    
}
