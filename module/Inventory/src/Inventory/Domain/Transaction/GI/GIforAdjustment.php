<?php
namespace Inventory\Domain\Transaction\GI;

use Inventory\Domain\Transaction\AbstractGoodsIssue;
use Inventory\Domain\Transaction\Contracts\GoodsIssueInterface;
use Inventory\Domain\Transaction\Contracts\StockQtyAdjustmentInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforAdjustment extends AbstractGoodsIssue implements GoodsIssueInterface, StockQtyAdjustmentInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GI_FOR_ADJUSTMENT_AFTER_COUNTING;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_OUT;
    }
}