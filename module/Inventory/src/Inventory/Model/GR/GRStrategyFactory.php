<?php
namespace Inventory\Model\GR;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRStrategyFactory
{

    public static function getGRStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING:
                return new GRfromPurchasing();
            default:
                throw new \Exception("Unknown Movement Type!");
        }
    }
    
    
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