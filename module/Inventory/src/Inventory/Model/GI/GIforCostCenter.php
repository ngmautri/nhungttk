<?php
namespace Inventory\Model\GI;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends \Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Inventory\Model\Constants::WH_TRANSACTION_OUT;
        
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_COST_CENTER;
    }

    public function validateRow($entity)
    {
        $errors = array();
        if (! $entity instanceof \Application\Entity\NmtInventoryTrx) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate("Invalid Argument! Inventory Moverment is not found.");
        }

        // OK to post
        // +++++++++++++++++++

        // Required Cost center;
        if ($entity->getCostCenter() == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate("Please give cost center! nmt");
        }

        return $errors;
    }

    public function doPosting($entity, $u)
    {}

    public function check($trx, $item, $u)
    {}

    public function reverse($entity, $u, $reversalDate)
    {}
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null)
    {}

}