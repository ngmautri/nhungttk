<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Event\GoodsReceiptPostedEvent;

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
    protected function doPost(TransactionPostingService $postingService = null, $notification)
    {
        try {
            // 1.validate header
            if ($this->getFifoService() == null) {
                $notification->addError("FIFO service (COGS) not found!");
            }

            $notification = $this->validate();

            if ($this->getFifoService() == null)
                $notification->addError("FIFO service (COGS) not found!");

            if ($notification->hasErrors())
                return $notification;

            $this->fifoService->createLayers($this);
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