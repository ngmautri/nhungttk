<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionCreatedEvent extends Event
{

    const EVENT_NAME = "inventory.transaction.created";

    protected $transactionId;

    public function __construct()
    {}
}