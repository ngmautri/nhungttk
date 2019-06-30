<?php
namespace Inventory\Domain\Warehouse\Transaction\Factory;

use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Warehouse\Transaction\GR\GRFromPurchasing;

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
}