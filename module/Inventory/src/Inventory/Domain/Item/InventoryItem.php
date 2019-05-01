<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItem extends AbstractItem
{

    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\AbstractItem::getItemType()
     */
    public function getItemType()
    {
        return ItemType::INVENTORY_ITEM_TYPE;
    }
}