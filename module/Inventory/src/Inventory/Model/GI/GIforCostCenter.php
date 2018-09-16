<?php
namespace Inventory\Model\GI;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforCostCenter extends AbstractGIStrategy
{
    public function doPosting($entity, $u)
    {}

    /**
     * @param \Application\Entity\NmtInventoryTrx $entity

     * {@inheritDoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::validateRow()
     */
    public function validateRow($entity)
    {
        $errors=array();
        if (! $entity instanceof \Application\Entity\NmtInventoryTrx) {
            $errors[]=$this->getContextService()->getControllerPlugin()->translate("Invalid Argument! Inventory Moverment is not found.");
        }
        
        // OK to post
        // +++++++++++++++++++
        
        // Required Cost center;
        if ($entity->getCostCenter() == null) {
            $errors[]=$this->getContextService()->getControllerPlugin()->translate("Please give cost center!");
        }
            
        return $errors;
    }

    public function check($trx, $item, $u)
    {}

    public function reverse($entity, $u, $reversalDate)
    {}
  
}