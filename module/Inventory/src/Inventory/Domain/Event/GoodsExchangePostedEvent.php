<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GoodsExchangePostedEvent extends Event
{

    protected $trx;

    const EVENT_NAME = "inventory.goods_exchange.posted";

    /**
     *
     * @param GenericTransaction $trx
     */
    public function __construct(GenericTransaction $trx)
    {
        $this->trx = $trx;
    }
}