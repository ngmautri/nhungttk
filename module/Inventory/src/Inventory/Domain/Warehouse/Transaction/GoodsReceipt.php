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
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::post()
     */
    public function post(TransactionPostingService $postingService = null)
    {
        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }
        
        // 1.validate header
        $notification = $this->validate();
        
        if ($this->getValuationService() == null)
            $notification->addError("Valuation service (COGS) not found!");
            
            if ($notification->hasErrors())
                return $notification;
                
                /**
                 * Template Method
                 * ===============
                 */
                $this->prePost($postingService);
                
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
                
                /**
                 * Template Method
                 * ==============
                 */
                $this->afterPost($postingService);
                
                return $notification;
    }
}