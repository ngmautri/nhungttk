<?php
namespace Procure\Model\DomainDriven\Entity\Doctrine;

use Procure\Model\DomainDriven\Entity\InterfacePurchaseRequest;

/**
 * Abtract Factory
 *
 * @author Nguyen Mau Tri
 *        
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
