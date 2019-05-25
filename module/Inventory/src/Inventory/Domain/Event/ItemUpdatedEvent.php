<?php
namespace Inventory\Domain\Event;

use Symfony\Component\Workflow\Event\Event;
use Inventory\Domain\Item\AbstractItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemUpdatedEvent extends Event{

    const EVENT_NAME = "inventory.item.updated";

    protected $item;

    /**
     *
     * @param AbstractItem $item
     */
    public function __construct(AbstractItem $item)
    {
        $this->item = $item;
    }

    /**
     *
     * @return \Inventory\Domain\Item\AbstractItem
     */
    public function getItem()
    {
        return $this->item;
    }
}