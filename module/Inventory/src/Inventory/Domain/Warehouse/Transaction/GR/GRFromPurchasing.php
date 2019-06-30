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

    public function specificRowValidationByFlow($row, $notification = null, $isPosting = false)
    {}

    public function afterPost()
    {}

    public function prePost()
    {}

    public function specificRowValidation($row, $notification = null, $isPosting = false)
    {}

}