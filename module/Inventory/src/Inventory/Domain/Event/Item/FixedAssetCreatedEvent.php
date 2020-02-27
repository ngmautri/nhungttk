<?php
namespace Inventory\Domain\Event\Item;

use Inventory\Domain\Item\GenericItem;
use Symfony\Component\Workflow\Event\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FixedAssetCreatedEvent extends Event{

    const EVENT_NAME = "fixed.asset.created";

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