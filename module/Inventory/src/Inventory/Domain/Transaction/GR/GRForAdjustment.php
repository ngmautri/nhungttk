<?php
namespace Inventory\Domain\Transaction\GR;

use Inventory\Domain\Transaction\AbstractGoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\StockQtyAdjustmentInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRForAdjustment extends AbstractGoodsReceipt implements GoodsReceiptInterface, StockQtyAdjustmentInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GR_FOR_ADJUSTMENT_AFTER_COUNTING;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }
}