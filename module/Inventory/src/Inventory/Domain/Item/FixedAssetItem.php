<?php
namespace Inventory\Domain\Item;
use Application\Notification;

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
     * @see \Inventory\Domain\Item\GenericItem::specificValidation()
     */
    public function specificValidation(Notification $notification = null)
    {}
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::FIXED_ASSET_ITEM_TYPE);        
        $this->setIsFixedAsset(1);        
    }


  
}