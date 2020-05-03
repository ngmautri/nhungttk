<?php
namespace Inventory\Domain\Transaction\GR;

use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromTransferLocation extends GoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\AbstractTransaction::specify()
     */
    public function __construct()
    {
        $this->movementType = TransactionType::GR_FROM_TRANSFER_LOCATION;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_IN;
    }
}