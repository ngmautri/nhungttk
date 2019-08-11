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
class GRFromPurchasing extends GoodsReceipt implements GoodsReceiptInterface
{
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\AbstractTransaction::specify()
     */
    public function specify()
    {
        $this->movementType =  TransactionType::GR_FROM_PURCHASING;
        $this->movementFlow =  TransactionFlow::WH_TRANSACTION_IN;
    }
    

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



    public function specificRowValidation($row, $notification = null, $isPosting = false)
    {}
    public function specificHeaderValidation($notification = null)
    {}
    protected function afterPost(TransactionPostingService $postingService = null)
    {}

    protected function prePost(TransactionPostingService $postingService = null)
    {}


    

}