<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionCreatedEvent extends Event{

    const EVENT_NAME = "inventory.transaction.created";

    protected $transactionId;

    /**
     *
     * @param GenericItem $item
     */
    public function __construct()
    {
    }

  
}