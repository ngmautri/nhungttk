<?php
namespace Inventory\Domain\Item;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItem extends GenericItem
{
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\GenericItem::validate()
     */
    public function validate()
    {
        
        $notification = new Notification();
        $notification->setSourceClass("nmt");
        
        if ($this->isNullOrBlank($this->getCreatedBy())) {
            $err = "User is not found.";
            $notification->addError($err);
        }
        
        if ($this->isNullOrBlank($this->getItemName())) {
            $err = "Item name is null or empty. It is required for any item.";
            $notification->addError($err);
        } else {
            
            if (preg_match('/[#$%*@]/', $this->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
        }
        
        if ($this->isNullOrBlank($this->getItemSku())) {
            $err = "Item SKU is null or empty. It is required for inventory item.";
            $notification->addError($err);
        }
        
        if ($this->getStandardUom() == null) {
            $err = "Item unit is empty or invalid. It is required for inventory item.";
            $notification->addError($err);
        }
            
        
        return $notification;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\GenericItem::getItemType()
     */
    public function getItemType()
    {
        return ItemType::INVENTORY_ITEM_TYPE;
    }
    
    

}