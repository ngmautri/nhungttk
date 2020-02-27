<?php
namespace Inventory\Domain\Event\Item;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemUpdatedEvent extends Event
{

    const EVENT_NAME = "inventory.item.updated";

    protected $item;

    /**
     *
     * @param GenericItem $item
     */
    public function __construct(GenericItem $item)
    {
        $this->item = $item;
    }

    /**
     *
     * @return \Inventory\Domain\Item\GenericItem
     */
    public function getItem()
    {
        return $this->item;
    }
}