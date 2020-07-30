<?php
namespace Inventory\Domain\Transaction\GR;

use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

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
        $this->specify();
    }

    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_PURCHASING;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }
}