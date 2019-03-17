<?php
namespace Inventory\Model;

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
     * @return \Inventory\Model\AbstractTransactionStrategy
     */
    public static function getMovementStrategy($tTransaction)
    {
        switch ($tTransaction) {
            
            // GOOD RECEIPT

            case \Inventory\Model\Constants::INVENTORY_GR_FROM_OPENNING_BALANCE:
                return new \Inventory\Model\GR\GRfromOpeningBalance();
                
            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING:
                return new \Inventory\Model\GR\GRfromPurchasing();

            case \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL:
                return new \Inventory\Model\GR\GRfromPurchasingReversal();

            // GOOD ISSUE
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX:
                return new \Inventory\Model\GI\GIforRepairMachine();

            case \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE:
                return new \Inventory\Model\GI\GIforRepairMachineWithoutExchange();
                
            case \Inventory\Model\Constants::INVENTORY_GI_FOR_COST_CENTER:
                return new \Inventory\Model\GI\GIforCostCenter();
                
            default:
                return null;
        }
    }
}