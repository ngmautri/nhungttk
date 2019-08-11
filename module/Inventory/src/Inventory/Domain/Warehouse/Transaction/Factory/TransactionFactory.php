<?php
namespace Inventory\Domain\Warehouse\Transaction\Factory;

use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Warehouse\Transaction\GR\GRFromPurchasing;
use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsReceipt;
use Inventory\Domain\Warehouse\Transaction\GI\GIforRepairMachine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionFactory
{

    public static function createTransaction($transactionTypeId)
    {
        switch ($transactionTypeId) {

            case TransactionType::GI_FOR_COST_CENTER:
                $trx = new GIforCostCenter();
                break;
                
            case TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX:
                $trx = new GIforRepairMachine();
                break;
                
            case TransactionType::GR_FROM_PURCHASING:
                $trx = new GRFromPurchasing();
                break;

            default:
                $trx = null;
                break;
        }

        if ($trx instanceof GenericTransaction)
            $trx->specify();

        return $trx;
    }

    /**
     *
     * @return string[]
     */
    public static function getSupportedTransaction()
    {
        $list = array();
        $list[] = TransactionType::GI_FOR_COST_CENTER;
        $list[] = TransactionType::GR_FROM_PURCHASING;
        return $list;
    }

    /**
     *
     * @param object $translator
     * @return string[][]|NULL[][]
     */
    public static function getGoodIssueTransactions($translator = null)
    {
        if ($translator !== null) {
            $list = array(

                TransactionType::GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => $translator->translate("Issue for reparing machine (no exchange of part)"),
                    "type_description" => $translator->translate("Issue part for repairing of machine. The requester must not give any part")
                ),

                TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => $translator->translate("Issue for reparing machine (exchange of part)"),
                    "type_description" => $translator->translate("Exchange parts for repairing of machine.<ul><li>Spare part controller will issue new parts and receive old /defect one back to store for disposal process.</li><li>Asset code is required.</li></ul>")
                ),

                TransactionType::GI_FOR_PROJECT => array(
                    "type_name" => $translator->translate("Issue for Project (e.g IE Project)"),
                    "type_description" => $translator->translate("Spare part, materials will be consumpted by the project.<br>Project must be given! ")
                ),

                TransactionType::GI_FOR_COST_CENTER => array(
                    "type_name" => $translator->translate("Issue for Cost Center"),
                    "type_description" => $translator->translate("Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! ")
                ),

                TransactionType::GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                TransactionType::GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => $translator->translate("Issue for Production Order"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for Production Order.<br>Production Order must be given! ")
                ),

                TransactionType::GI_FOR_EXTERNAL_REPAIR => array(
                    "type_name" => $translator->translate("Issue for Repairing"),
                    "type_description" => $translator->translate("Spare part, materials will be repaired by external party. After repaired, part will be received again")
                ),

                TransactionType::GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => $translator->translate("Issue for Maintenance Work"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for maintenance  worked.")
                ),

                TransactionType::GI_FOR_ASSET => array(
                    "type_name" => $translator->translate("Issue for Asset"),
                    "type_description" => $translator->translate("Spare part, materials will be issued for an asset. Asset ID is required.")
                ),

                TransactionType::GI_FOR_RETURN_PO => array(
                    "type_name" => $translator->translate("Issue for return PO"),
                    "type_description" => $translator->translate("goods will be issued for returning to supplier. PO is required!")
                ),

                TransactionType::GI_FOR_TRANSFER_WAREHOUSE => array(
                    "type_name" => $translator->translate("Transfer to other warehouse"),
                    "type_description" => $translator->translate("goods will be issued for other warehouse.")
                ),

                TransactionType::GI_FOR_TRANSFER_LOCATION => array(
                    "type_name" => $translator->translate("Transfer to other location"),
                    "type_description" => $translator->translate("goods will be issued for other location in warehouse.")
                ),

                TransactionType::GI_FOR_DISPOSAL => array(
                    "type_name" => $translator->translate("Issue for diposal"),
                    "type_description" => $translator->translate("goods will be disposed.PO is required!")
                )
            );
        } else {
            $list = array(

                TransactionType::GI_FOR_REPAIR_MACHINE => array(
                    "type_name" => "Issue for reparing machine without exchange",
                    "type_description" => "Issue part for repairing of machine. The requester must not give any part"
                ),

                TransactionType::GI_FOR_REPAIR_MACHINE_WITH_EX => array(
                    "type_name" => "Issue for reparing machine (exchange of part)",
                    "type_description" => "Issue part for repairing of machine. The requester must return old /defect part."
                ),

                TransactionType::GI_FOR_PROJECT => array(
                    "type_name" => "Issue for Project (e.g IE Project)",
                    "type_description" => "Spare part, materials will be consumpted by the project.<br>Project must be given! "
                ),

                TransactionType::GI_FOR_COST_CENTER => array(
                    "type_name" => "Issue for Cost Center",
                    "type_description" => "Spare part, materials will be consumpted by an cost center.<br>Cost Center must be given! "
                ),

                TransactionType::GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_description" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                TransactionType::GI_FOR_PRODUCTION_ORDER => array(
                    "type_name" => "Issue for Production Order",
                    "type_description" => "Spare part, materials will be issued for Production Order.<br>Production Order must be given! "
                ),

                TransactionType::GI_FOR_EXTERNAL_REPAIR => array(
                    "type_name" => "Issue for Repairing",
                    "type_description" => "Spare part, materials will be repaired by external party. After repaired, part will be received again"
                ),

                TransactionType::GI_FOR_MAINTENANCE_WORK => array(
                    "type_name" => "Issue for Maintenance Work",
                    "type_description" => "Spare part, materials will be issued for maintenance  worked."
                ),

                TransactionType::GI_FOR_ASSET => array(
                    "type_name" => "Issue for Asset",
                    "type_description" => "Spare part, materials will be issued for an asset. Asset ID is required."
                ),

                TransactionType::GI_FOR_RETURN_PO => array(
                    "type_name" => "Issue for return PO",
                    "type_description" => "goods will be issued for returning to supplier. PO is required!"
                ),
                TransactionType::GI_FOR_DISPOSAL => array(
                    "type_name" => "Issue for diposal",
                    "type_description" => "goods will be disposed"
                )
            );
        }

        return $list;
    }

    /**
     *
     * @param object $translator
     * @return NULL[][]
     */
    public static function getGoodReceiptTransactions($translator = null)
    {
        if ($translator != null) {
            $list = array(

                TransactionType::GR_FROM_PURCHASING => array(
                    "type_name" => $translator->translate("Goods receipt from purchase"),
                    "type_description" => $translator->translate("Goods receipt from purchase")
                )
            );
        } else {
            $list = array(

                TransactionType::GR_FROM_PURCHASING => array(
                    "type_name" => $translator->translate("Goods receipt from purchase"),
                    "type_description" => $translator->translate("Goods receipt from purchase")
                )
            );
        }

        return $list;
    }
}