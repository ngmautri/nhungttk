<?php
namespace Inventory\Model\GI;

use Inventory\Model\AbstractTransactionStrategy;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforRepair extends AbstractTransactionStrategy
{
    
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Application\Model\Constants::WH_TRANSACTION_OUT;
        
    }
    
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_PRODUCTION_ORDER;
    }
    
    public function execute()
    {}
    public function doPosting($entity, $u)
    {}

    public function validateRow($entity)
    {}

    public function check($trx, $item, $u)
    {}

    public function reverse($entity, $u, $reversalDate)
    {}
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null)
    {}




  
}