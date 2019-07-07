<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionPostedEvent extends Event
{

    const EVENT_NAME = "inventory.transaction.posted";

    public function __construct()
    {}
}