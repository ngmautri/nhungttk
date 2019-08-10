<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GoodIssuePostedEvent extends Event
{
    
    protected $trx;

    const EVENT_NAME = "inventory.good_issue.posted";

    /**
     * 
     * @param GenericTransaction $trx
     */
    public function __construct(GenericTransaction $trx)
    {
        $this->trx = $trx; 
    }
}