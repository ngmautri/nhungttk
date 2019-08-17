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
    protected function doPost(TransactionPostingService $postingService = null)
    {
      
        // 1.validate header
        $notification = $this->validate();

        if ($this->getValuationService() == null)
            $notification->addError("Valuation service (COGS) not found!");

        if ($notification->hasErrors())
            return $notification;

        // 2. caculate cogs
        foreach ($this->transactionRows as $row) {

            /**
             *
             * @var \Inventory\Domain\Warehouse\Transaction\TransactionRow $row ;
             */
            $cogs = $this->getValuationService()->calculateCOGS($this, $row);
            echo ($cogs);
            $snapshot = $row->makeSnapshot();
            $snapshot->cogsLocal = $cogs;
            $row->makeFromSnapshot($snapshot);
        }

        $postingService->getTransactionCmdRepository()->post($this, true);

        // Recording Events
        $this->recoredEvents[] = new GoodsReceiptPostedEvent($this);
                  
                return $notification;
    }
}