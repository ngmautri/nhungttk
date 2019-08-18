<?php
namespace Inventory\Domain\Warehouse\Transaction\GR;

use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Warehouse\Transaction\GoodsReceipt;
use Inventory\Domain\Warehouse\Transaction\GoodsReceiptInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromOpeningBalance extends GoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\AbstractTransaction::specify()
     */
    public function specify()
    {
        $this->movementType = TransactionType::GR_FROM_OPENNING_BALANCE;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_IN;
    }
    
    protected function specificRowValidationByFlow($row, $notification = null, $isPosting = false)
    {}

    protected function afterPost(TransactionPostingService $postingService = null, $notification = null)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::prePost()
     */
    protected function prePost(TransactionPostingService $postingService = null, $notification = null)
    {
        // Need to implemented.
        
    }

    protected function specificValidation($notification = null)
    {}

    protected function specificHeaderValidation($notification = null)
    {}

    protected function specificRowValidation($row, $notification = null, $isPosting = false)
    {}

    public function addTransactionRow($transactionRow)
    {}

}