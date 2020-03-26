<?php
namespace Inventory\Domain\Warehouse\Transaction\GR;

use Application\Notification;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;
use Inventory\Domain\Warehouse\Transaction\GoodsReceipt;
use Inventory\Domain\Warehouse\Transaction\GoodsReceiptInterface;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRWithInvoice extends GoodsReceipt implements GoodsReceiptInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\AbstractTransaction::specify()
     */
    public function specify()
    {
        $this->movementType = TransactionType::GR_WITH_INVOICE;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_IN;
    }

    protected function specificRowValidationByFlow(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false)
    {}

    protected function afterPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {}

    protected function prePost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {}

    protected function specificValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}

    protected function specificHeaderValidation(TransactionSpecificationService $specificationService, Notification $notification = null)
    {}

    protected function specificRowValidation(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false)
    {}

    public function addTransactionRow(TransactionRow $transactionRow)
    {}
}