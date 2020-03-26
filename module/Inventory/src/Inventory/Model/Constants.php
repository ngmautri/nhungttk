<?php
namespace Inventory\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{

    const INVENTORY_GI_FOR_REPAIR_MACHINE = 'GI099';

    const INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX = 'GI100';

    const INVENTORY_GI_FOR_PROJECT = 'GI101';

    const INVENTORY_GI_FOR_COST_CENTER = 'GI102';

    const INVENTORY_GI_FOR_PRODUCTION_ORDER = 'GI103';

    const INVENTORY_GI_FOR_RETURN_PO = 'GI104';

    const INVENTORY_GI_FOR_REPAIRING = 'GI105';

    const INVENTORY_GI_FOR_MAINTENANCE_WORK = 'GI106';

    const INVENTORY_GI_FOR_ASSET = 'GI107';

    const INVENTORY_GI_FOR_TRANSFER_WAREHOUSE = 'GI108';

    const INVENTORY_GI_FOR_TRANSFER_LOCATION = 'GI109';

    const INVENTORY_GI_FOR_DISPOSAL = 'GI999';

    // ========================================
    const INVENTORY_GR_FROM_OPENNING_BALANCE = 'GR000';

    const INVENTORY_GR_FROM_PURCHASING = 'GR100';

    const INVENTORY_GR_FROM_TRANSFER_WAREHOUSE = 'GR101';

    const INVENTORY_GR_FROM_TRANSFER_LOCATION = 'GR102';

    const INVENTORY_GR_FROM_PURCHASING_REVERSAL = 'GR100-1';

    // ========================================
    const INVENTORY_TRANSFER_WAREHOUSE = 'warehouse';

    const INVENTORY_TRANSFER_LOCATION = 'location';

    // ========================================
    const WH_TRANSACTION_IN = 'IN';

    const WH_TRANSACTION_OUT = 'OUT';

    // ========================================

    /**
     *
     * @param object $translator
     * @return string[][]|NULL[][]
     */
    public static function getGoodsIssueTypes($translator = null)
    {
        if ($translator !== null) {
            $list = array(

                self::INVENTORY_GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => $translator->translate("Issue for reparing machine (no exchange of part)"),
                    "type_description" => $translator->translate("Issue part for repairing of machine. The requester must not give any part")
                ),

                self::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => $translator->translate("Issue for reparing machine (exchange of part)"),
                    "type_description" => $translator->translate("Exchange parts for repairing of machine.<ul><li>Spare part controller will issue new parts and receive old /defect one back to store for disposal process.</li><li>Asset code is required.</li></ul>")
                ),

                self::INVENTORY_GI_FOR_PROJECT => array(
                    "type_name" => $translator->translate("Issue for Project (e.g IE Project)"),
                    "type_description" => $translator->translate("Spare part, materials will be consumpted by the project.<br>Project must be given! ")
                ),

                self::INVENTORY_GI_FOR_COST_CENTER => array(
                    "type_name" => $translator->translate("Issue for Cost Center"),
                    "type_description" => $translator->translate("Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! ")
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                self::INVENTORY_GI_FOR_REPAIRING => array(
                    "type_name" => $translator->translate("Issue for Repairing"),
                    "type_description" => $translator->translate("Spare part, materials will be repaired by external party. After repaired, part will be received again")
                ),

                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => $translator->translate("Issue for Maintenance Work"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for maintenance  worked.")
                ),

                self::INVENTORY_GI_FOR_ASSET => array(
                    "type_name" => $translator->translate("Issue for Asset"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for an asset. Asset ID is required.")
                ),

                self::INVENTORY_GI_FOR_RETURN_PO => array(
                    "type_name" => $translator->translate("Issue for return PO"),
                    "type_description" => $translator->translate("goods will be issued for returning to supplier. PO is required!")
                ),

                self::INVENTORY_GI_FOR_TRANSFER_WAREHOUSE => array(
                    "type_name" => $translator->translate("Transfer to other warehouse"),
                    "type_description" => $translator->translate("goods will be issued for other warehouse.")
                ),

                self::INVENTORY_GI_FOR_TRANSFER_LOCATION => array(
                    "type_name" => $translator->translate("Transfer to other location"),
                    "type_description" => $translator->translate("goods will be issued for other location in warehouse.")
                ),

                self::INVENTORY_GI_FOR_DISPOSAL => array(
                    "type_name" => $translator->translate("Issue for diposal"),
                    "type_description" => $translator->translate("goods will be disposed.PO is required!")
                )
            );
        } else {
            $list = array(

                self::INVENTORY_GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => "Issue for reparing machine without exchange",
                    "type_description" => "Issue part for repairing of machine. The requester must not give any part"
                ),

                self::INVENTORY_GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => "Issue for reparing machine (exchange of part)",
                    "type_description" => "Issue part for repairing of machine. The requester must return old /defect part."
                ),

                self::INVENTORY_GI_FOR_PROJECT => array(
                    "type_name" => "Issue for Project (e.g IE Project)",
                    "type_description" => "Spare part, materials will be consumpted by the project.<br>Project must be given! "
                ),

                self::INVENTORY_GI_FOR_COST_CENTER => array(
                    "type_name" => "Issue for Cost Center",
                    "type_description" => "Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! "
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_description" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_description" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                self::INVENTORY_GI_FOR_REPAIRING => array(
                    "type_name" => "Issue for Repairing",
                    "type_description" => "Spare part, materials will be repaired by external party. After repaired, part will be received again"
                ),

                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => "Issue for Maintenance Work",
                    "type_description" => "Spare part, materials will be issued for maintenance  worked."
                ),

                self::INVENTORY_GI_FOR_ASSET => array(
                    "type_name" => "Issue for Asset",
                    "type_description" => "Spare part, materials will be issued for an asset. Asset ID is required."
                ),

                self::INVENTORY_GI_FOR_RETURN_PO => array(
                    "type_name" => "Issue for return PO",
                    "type_description" => "goods will be issued for returning to supplier. PO is required!"
                ),
                self::INVENTORY_GI_FOR_DISPOSAL => array(
                    "type_name" => "Issue for diposal",
                    "type_description" => "goods will be disposed. PO is required!"
                )
            );
        }

        return $list;
    }

    /**
     *
     * @param string $movermentType
     * @param object $translator
     * @return string|NULL
     */
    public static function getGoodsIssueType($movementType, $translator = null)
    {
        $list = self::getGoodsIssueTypes($translator);

        if (isset($list[$movementType])) {
            return $list[$movementType];
        }

        return null;
    }

    /**
     *
     * @param array $translator
     * @return NULL[][]
     */
    public static function getGoodsReceiptTypes($translator = null)
    {
        if ($translator != null) {
            $list = array(

                self::INVENTORY_GR_FROM_PURCHASING => array(
                    "type_name" => $translator->translate("Goods receipt from purchase"),
                    "type_description" => $translator->translate("Goods receipt from purchase")
                )
            );
        } else {
            $list = array(

                self::INVENTORY_GR_FROM_PURCHASING => array(
                    "type_name" => $translator->translate("Goods receipt from purchase"),
                    "type_description" => $translator->translate("Goods receipt from purchase")
                )
            );
        }

        return $list;
    }
}

