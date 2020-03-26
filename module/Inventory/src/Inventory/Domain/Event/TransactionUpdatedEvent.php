<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionUpdatedEvent extends Event
{

    const EVENT_NAME = "inventory.transaction.updated";

    protected $transactionId;

    public function __construct()
    {}
}