<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ServiceItem extends AbstractItem
{
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\AbstractItem::getItemType()
     */
    public function getItemType()
    {
        return ItemType::SERVICE_ITEM_TYPE;
    }
}