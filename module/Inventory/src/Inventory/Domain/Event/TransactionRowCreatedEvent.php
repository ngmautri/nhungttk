<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowCreatedEvent extends Event
{

    const EVENT_NAME = "inventory.transaction_row.created";

    public function __construct()
    {}
}