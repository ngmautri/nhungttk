<?php
namespace Inventory\Domain\Warehouse\Transaction\Factory;

use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Warehouse\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

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
            default:
                $trx = null;
                break;
        }
         
        if($trx instanceof GenericTransaction)
            $trx->specify();
        

        return $trx;
    }
}