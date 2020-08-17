<?php
namespace Inventory\Domain\Transaction\Contracts;

use Application\Domain\Util\Translator;

/**
 * Goods Movement Type (GM, WT)
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxType
{

    const GI_FOR_REPAIR_MACHINE = 'GI099';

    const GI_FOR_REPAIR_MACHINE_WITH_EX = 'GI100';

    const GI_FOR_PROJECT = 'GI101';

    const GI_FOR_COST_CENTER = 'GI102';

    const GI_FOR_PRODUCTION_ORDER = 'GI103';

    const GI_FOR_RETURN_PO = 'GI104';

    const GI_FOR_EXTERNAL_REPAIR = 'GI105';

    const GI_FOR_MAINTENANCE_WORK = 'GI106';

    const GI_FOR_ASSET = 'GI107';

    const GI_FOR_TRANSFER_WAREHOUSE = 'GI108';

    const GI_FOR_TRANSFER_LOCATION = 'GI109';

    const GI_FOR_DISPOSAL = 'GI999';

    // ========================================
    const GR_FROM_OPENNING_BALANCE = 'GR000';

    const GR_FROM_PURCHASING = 'GR100';

    const GR_FROM_TRANSFER_WAREHOUSE = 'GR101';

    const GR_FROM_TRANSFER_LOCATION = 'GR102';

    const GR_FROM_PURCHASING_REVERSAL = 'GR100-1';

    const GR_FROM_EXCHANGE = 'GR103';

    const GR_WITH_INVOICE = 'GR104';

    const GR_WITHOUT_INVOICE = 'GR105';

    public static function getSupportedTransaction()
    {
        $list = array();
        $list[] = self::GI_FOR_REPAIR_MACHINE;
        $list[] = self::GI_FOR_REPAIR_MACHINE_WITH_EX;
        $list[] = self::GI_FOR_PROJECT;
        $list[] = self::GI_FOR_COST_CENTER;
        $list[] = self::GI_FOR_RETURN_PO;
        $list[] = self::GI_FOR_EXTERNAL_REPAIR;
        $list[] = self::GI_FOR_ASSET;
        $list[] = self::GI_FOR_TRANSFER_WAREHOUSE;
        $list[] = self::GI_FOR_TRANSFER_LOCATION;
        $list[] = self::GI_FOR_DISPOSAL;

        $list[] = self::GR_FROM_OPENNING_BALANCE;
        $list[] = self::GR_FROM_PURCHASING;
        $list[] = self::GR_FROM_TRANSFER_WAREHOUSE;
        $list[] = self::GR_FROM_PURCHASING_REVERSAL;
        $list[] = self::GR_FROM_EXCHANGE;
        $list[] = self::GR_WITHOUT_INVOICE;

        return $list;
    }

    public static function getGoodIssueTrx()
    {
        return [

            self::GI_FOR_REPAIR_MACHINE => [
                "type_name" => Translator::translate("Issue for reparing machine (no exchange of part)"),
                "type_description" => Translator::translate("Issue part for repairing of machine. The requester must not give any part")
            ],

            self::GI_FOR_REPAIR_MACHINE_WITH_EX => [
                "type_name" => Translator::translate("Issue for reparing machine (exchange of part)"),
                "type_description" => Translator::translate("Exchange parts for repairing of machine.<br>- Spare part controller will issue new parts and receive old /defect one back to store for disposal process.<br>- Asset code is required.")
            ],

            self::GI_FOR_PROJECT => [
                "type_name" => Translator::translate("Issue for Project (e.g IE Project)"),
                "type_description" => Translator::translate("Spare part, materials will be consumpted by the project.<br>Project must be given! ")
            ],

            self::GI_FOR_COST_CENTER => [
                "type_name" => Translator::translate("Issue for Cost Center"),
                "type_description" => Translator::translate("Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! ")
            ],

            self::GI_FOR_PRODUCTION_ORDER => array(
                "type_name" => Translator::translate("Issue for Production Order"),
                "type_description" => Translator::translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
            ),

            self::GI_FOR_PRODUCTION_ORDER => array(
                "type_name" => Translator::translate("Issue for Production Order"),
                "type_description" => Translator::translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
            ),

            self::GI_FOR_EXTERNAL_REPAIR => array(
                "type_name" => Translator::translate("Issue for Repairing"),
                "type_description" => Translator::translate("Spare part, materials will be repaired by external party. After repaired, part will be received again")
            ),

            self::GI_FOR_MAINTENANCE_WORK => array(
                "type_name" => Translator::translate("Issue for Maintenance Work"),
                "type_description" => Translator::translate("Spare part, materials will be issued for maintenance  worked.")
            ),

            self::GI_FOR_ASSET => array(
                "type_name" => Translator::translate("Issue for Asset"),
                "type_description" => Translator::translate("Spare part, materials will be issued for an asset. Asset ID is required.")
            ),

            self::GI_FOR_RETURN_PO => array(
                "type_name" => Translator::translate("Issue for return PO"),
                "type_description" => Translator::translate("goods will be issued for returning to supplier. PO is required!")
            ),

            self::GI_FOR_TRANSFER_WAREHOUSE => array(
                "type_name" => Translator::translate("Transfer to other warehouse"),
                "type_description" => Translator::translate("goods will be issued for other warehouse.")
            ),

            self::GI_FOR_TRANSFER_LOCATION => array(
                "type_name" => Translator::translate("Transfer to other location"),
                "type_description" => Translator::translate("goods will be issued for other location in warehouse.")
            ),

            self::GI_FOR_DISPOSAL => array(
                "type_name" => Translator::translate("Issue for diposal"),
                "type_description" => Translator::translate("goods will be disposed.PO is required!")
            )
        ];
    }

    public static function getGoodReceiptTrx()
    {
        return [

            self::GR_FROM_OPENNING_BALANCE => [
                "type_name" => Translator::translate("Initiate openning balance"),
                "type_description" => Translator::translate("Openning balance")
            ],

            self::GR_FROM_PURCHASING => [
                "type_name" => Translator::translate("Good Receipt from purchasing"),
                "type_description" => Translator::translate("Good Receipt from purchasing. This transaction is created automatically when procurment enter goods receipt. if goods does not meat requirment, please create return transaction!")
            ],
            self::GR_FROM_TRANSFER_WAREHOUSE => [
                "type_name" => Translator::translate("Good Receipt from warehouse transfer"),
                "type_description" => Translator::translate("Good Receipt from warehouse transfer")
            ],
            self::GR_FROM_EXCHANGE => [
                "type_name" => Translator::translate("Good Receipt from exchange)"),
                "type_description" => Translator::translate("Good Receipt from exchange")
            ],
            self::GR_WITHOUT_INVOICE => [
                "type_name" => Translator::translate("Good Receipt of free spare part)"),
                "type_description" => Translator::translate("Receipt free spare part - Zero Valua invoice")
            ]
        ];
    }
}