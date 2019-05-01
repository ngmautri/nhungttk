<?php
namespace Inventory\Domain\Item;



/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NoneInventoryItem extends AbstractItem
{
    
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\AbstractItem::getItemType()
     */
    public function getItemType()
    {
        return ItemType::NONE_INVENTORY_ITEM_TYPE;
    }
}