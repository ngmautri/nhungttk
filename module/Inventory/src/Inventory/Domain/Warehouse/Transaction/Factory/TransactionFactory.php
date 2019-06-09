<?php
namespace Inventory\Domain\Warehouse\Factory;

use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Inventory\Domain\Warehouse\Transaction\GI\GIforCostCenter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionFactory
{

        public static function getFactory($transactionTypeId)
    {
        switch ($transactionTypeId) {

            case TransactionType::GI_FOR_COST_CENTER:
                $trx = new GIforCostCenter();
                break;
            default:
                $trx = null;
                break;
        }

        return $trx;
    }

 
}