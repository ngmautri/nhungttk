<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Inventory\Domain\Event\GoodsReceiptPostedEvent;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GoodsReceipt extends GenericTransaction
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::doPost()
     */
    protected function doPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {
        try {

            if ($notification == null)
                $notification = new Notification();

            if ($specificationService == null)
                $notification->addError("Specification service (COGS) not found!");

            if ($valuationService == null)
                $notification->addError("Valuation service (COGS) not found!");

            if ($postingService == null)
                $notification->addError("Posting service  not found!");

            if ($notification->hasErrors())
                return $notification;

            // 1.validate transaction
            $notification = $this->validate($specificationService, $notification, true);

            if ($notification->hasErrors())
                return $notification;

            $valuationService->getFifoService()->createLayersFor($this);
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::raiseEvent()
     */
    protected function raiseEvent()
    {
        // Recording Events
        $this->recordedEvents[] = new GoodsReceiptPostedEvent($this);
    }
}