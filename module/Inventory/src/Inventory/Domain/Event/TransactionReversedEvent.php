<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionReversedEvent extends Event
{

    const EVENT_NAME = "inventory.transaction.reversed";

    public function __construct()
    {}
}