<?php
namespace Inventory\Model\GR;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryTransactionStrategyFactory
{
    
    /**
     * 
     * @param string $tTransaction
     * @throws \Exception
     * @return \Inventory\Model\GR\GRfromPurchasing|\Inventory\Model\GR\GRfromPurchasingReversal
     */
    public static function getGRStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING:
                return new GRfromPurchasing();
                
            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL:
                return new GRfromPurchasingReversal();
                
            default:
                throw new \Exception("Unknown Movement Type!");
        }
    }
    
    /**
     * 
     * @param String $tTransaction
     * @throws \Exception
     * @return \Inventory\Model\GR\GRfromPurchasing
     */
    public static function getGRType($tTransaction)
    {
        switch ($tTransaction) {
            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING:
                return new GRfromPurchasing();
            default:
                throw new \Exception("Unknown Movement Type!");
        }
    }
    
}