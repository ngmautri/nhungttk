<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GoodsReceipt;
use Inventory\Domain\Warehouse\Transaction\GoodsReceiptInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasing extends GoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificValidation()
     */
    public function specificValidation($notification = null)
    {
        // empty
    }

    public function addTransactionRow($transactionRowDTO)
    {}
}