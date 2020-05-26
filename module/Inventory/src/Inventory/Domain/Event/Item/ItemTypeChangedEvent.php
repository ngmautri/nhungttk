<?php
namespace Inventory\Domain\Event\Item;

use Application\Application\Event\AbstractEvent;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemTypeChangedEvent extends AbstractEvent
{

    const EVENT_NAME = "item.type.changed";

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