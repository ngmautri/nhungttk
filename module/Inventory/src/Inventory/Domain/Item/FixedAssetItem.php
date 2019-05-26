<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FixedAssetItem extends GenericItem
{
   /**
    * 
    * {@inheritDoc}
    * @see \Inventory\Domain\Item\GenericItem::getItemType()
    */
    public function getItemType()
    {
        return ItemType::FIXED_ASSET_ITEM_TYPE;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\GenericItem::validate()
     */
    public function validate()
    {}

}