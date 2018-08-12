<?php
namespace Inventory\Model;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Constants
{

    const INVENTORY_GI_FOR_REPAIR_MACHINE = 'GI100';

    const INVENTORY_GI_FOR_PROJECT = 'GI101';

    const INVENTORY_GI_FOR_COST_CENTER = 'GI102';

    const INVENTORY_GI_FOR_PRODUCTION_ORDER = 'GI103';

    const INVENTORY_GI_FOR_RETURN_PO = 'GI104';

    const INVENTORY_GI_FOR_REPAIRING = 'GI105';

    const INVENTORY_GI_FOR_MAINTENANCE_WORK = 'GI106';

    const INVENTORY_GI_FOR_ASSET = 'GI107';
    
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
                self::INVENTORY_GI_FOR_REPAIR_MACHINE => $translator->translate("Issue for reparing machine"),
                self::INVENTORY_GI_FOR_PROJECT => $translator->translate("Issue for Project (incl.IE)"),
                self::INVENTORY_GI_FOR_COST_CENTER => $translator->translate("Issue for Cost Center"),
                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => $translator->translate("Issue for Production Order"),
                self::INVENTORY_GI_FOR_REPAIRING => $translator->translate("Issue for Repairing"),
                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => $translator->translate("Issue for Maintenance Work"),
                self::INVENTORY_GI_FOR_ASSET => $translator->translate("Issue for Asset"),
            );
        }else{
            $list = array(
                self::INVENTORY_GI_FOR_REPAIR_MACHINE => "Issue for reparing machine",
                self::INVENTORY_GI_FOR_PROJECT => "Issue for Project (incl.IE)",
                self::INVENTORY_GI_FOR_COST_CENTER => "Issue for Cost Center",
                self::INVENTORY_GI_FOR_PRODUCTION_ORDER => "Issue for Production Order",
                self::INVENTORY_GI_FOR_REPAIRING => "Issue for Repairing",
                self::INVENTORY_GI_FOR_MAINTENANCE_WORK => "Issue for Maintenance Work",
                self::INVENTORY_GI_FOR_ASSET => "Issue for Asset"
            );
        }

        return $list;
    }

    public static function getGoodsReceiptTypes()
    {}
}

