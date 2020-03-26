<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionPostedEvent extends Event
{

    protected $trx;

    const EVENT_NAME = "inventory.transaction.posted";

    /**
     *
     * @param GenericTransaction $trx
     */
    public function __construct(GenericTransaction $trx)
    {
        $this->trx = $trx;
    }
}