<?php
namespace Inventory\Model\GI;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforRepair extends AbstractGIStrategy
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
    
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_COST_CENTER;
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



  
}