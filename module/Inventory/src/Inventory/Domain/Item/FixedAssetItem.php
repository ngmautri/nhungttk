<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FixedAssetItem extends AbstractItem
{
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\AbstractItem::getItemType()
     */
    public function getItemType()
    {
        return ItemType::FIXED_ASSET_ITEM_TYPE;
    }
}