<?php
namespace Inventory\Domain\Transaction\GR;

use Inventory\Domain\Transaction\AbstractGoodsReceipt;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRWithoutInvoice extends AbstractGoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GR_WITHOUT_INVOICE;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }

    public function __construct()
    {
        $this->specify();
    }
}