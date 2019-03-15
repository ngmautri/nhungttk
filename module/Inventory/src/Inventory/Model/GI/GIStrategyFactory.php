<?php
namespace Inventory\Model\GI;


/**
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIStrategyFactory
{

    public static function getGIStrategy($tTransaction)
    {
        switch ($tTransaction) {
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX:
                return new GIforRepairMachine();
            
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE:
                return new GIforRepairMachineWithoutExchange();
                
                
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_COST_CENTER:
                return new GIforCostCenter();
              default:
                throw new \Exception("Unknown Movement Type!");
        }
    }
    
    
    public static function getGIType($tTransaction)
    {
        switch ($tTransaction) {
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE:
                return new GIforRepairMachine();
            default:
                throw new \Exception("Unknown Movement Type!");
        }
    }
    
}