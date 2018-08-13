<?php
namespace Inventory\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{

    const INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX = 'GI099';

    const INVENTORY_GI_FOR_REPAIR_MACHINE = 'GI100';

    const INVENTORY_GI_FOR_PROJECT = 'GI101';

    const INVENTORY_GI_FOR_COST_CENTER = 'GI102';

    const INVENTORY_GI_FOR_PRODUCTION_ORDER = 'GI103';

    const INVENTORY_GI_FOR_RETURN_PO = 'GI104';

    const INVENTORY_GI_FOR_REPAIRING = 'GI105';

    const INVENTORY_GI_FOR_MAINTENANCE_WORK = 'GI106';

    const INVENTORY_GI_FOR_ASSET = 'GI107';
    
    const INVENTORY_GI_FOR_DISPOSAL = 'GI999';
    
    
    const WH_TRANSACTION_IN = 'IN';

    const WH_TRANSACTION_OUT = 'OUT';

    /**
     *
     * @return string[]
     */
    public static function getGoodsIssueTypes($translator = null)
    {
        if ($translator !== null) {
            $list = array(

                self::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => $translator->translate("Issue for reparing machine (exchange of part)"),
                    "type_description" => $translator->translate("Issue part for repairing of machine. The requester must exchange old /defect part")
                ),

                self::INVENTORY_GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => $translator->translate("Issue for reparing machine (no exchange)"),
                    "type_descripption" => $translator->translate("Issue part for repairing of machine. The requester must not give any part")
                ),

                self::INVENTORY_GI_FOR_PROJECT => array(
                    "type_name" => $translator->translate("Issue for Project (e.g IE Project)"),
                    "type_descripption" => $translator->translate("Spare part, materials will be consumpted by the project.<br>Project must be given! ")
                ),

                self::INVENTORY_GI_FOR_COST_CENTER => array(
                    "type_name" => $translator->translate("Issue for Cost Center"),
                    "type_descripption" => $translator->translate("Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! ")
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_descripption" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_descripption" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                self::INVENTORY_GI_FOR_REPAIRING => array(
                    "type_name" => $translator->translate("Issue for Repairing"),
                    "type_descripption" => $translator->translate("Spare part, materials will be repaired by external party. After repaired, part will be received again")
                ),

                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => $translator->translate("Issue for Maintenance Work"),
                    "type_descripption" => $translator->translate("Spare part, materials will be issued for maintenance  worked.")
                ),

                self::INVENTORY_GI_FOR_ASSET => array(
                    "type_name" => $translator->translate("Issue for Asset"),
                    "type_descripption" => $translator->translate("Spare part, materials will be issued for an asset. Asset ID is required.")
                ),

                self::INVENTORY_GI_FOR_RETURN_PO => array(
                    "type_name" => $translator->translate("Issue for return PO"),
                    "type_descripption" => $translator->translate("goods will be issued for returning to supplier. PO is required!")
                ),
                
                self::INVENTORY_GI_FOR_DISPOSAL => array(
                    "type_name" => $translator->translate("Issue for diposal"),
                    "type_descripption" => $translator->translate("goods will be disposed.PO is required!")
                )
            );
        } else {
            $list = array(

                self::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => "Issue for reparing machine (exchange of part)",
                    "type_description" => "Issue part for repairing of machine. The requester must exchange old /defect part"
                ),

                self::INVENTORY_GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => "Issue for reparing machine without exchange",
                    "type_descripption" => "Issue part for repairing of machine. The requester must not give any part"
                ),

                self::INVENTORY_GI_FOR_PROJECT => array(
                    "type_name" => "Issue for Project (e.g IE Project)",
                    "type_descripption" => "Spare part, materials will be consumpted by the project.<br>Project must be given! "
                ),

                self::INVENTORY_GI_FOR_COST_CENTER => array(
                    "type_name" => "Issue for Cost Center",
                    "type_descripption" => "Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! "
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_descripption" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_descripption" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                self::INVENTORY_GI_FOR_REPAIRING => array(
                    "type_name" => "Issue for Repairing",
                    "type_descripption" => "Spare part, materials will be repaired by external party. After repaired, part will be received again"
                ),

                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => "Issue for Maintenance Work",
                    "type_descripption" => "Spare part, materials will be issued for maintenance  worked."
                ),

                self::INVENTORY_GI_FOR_ASSET => array(
                    "type_name" => "Issue for Asset",
                    "type_descripption" => "Spare part, materials will be issued for an asset. Asset ID is required."
                ),

                self::INVENTORY_GI_FOR_RETURN_PO => array(
                    "type_name" => "Issue for return PO",
                    "type_descripption" => "goods will be issued for returning to supplier. PO is required!"
                ),
                self::INVENTORY_GI_FOR_DISPOSAL => array(
                    "type_name" => "Issue for diposal",
                    "type_descripption" => "goods will be disposed. PO is required!"
                )
            );
        }

        return $list;
    }

    public static function getGoodsReceiptTypes()
    {}
}

