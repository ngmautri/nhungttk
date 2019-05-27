<?php
namespace Inventory\Domain\Item;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ServiceItem extends GenericItem
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\GenericItem::validate()
     */
    public function validate()
    {
        $notification = new Notification();
        $notification->setSourceClass(get_class($this));
        
        if ($this->isNullOrBlank($this->getItemName())) {
            $err = "Item name is null or empty. It is required for any item.";
            $notification->addError($err);
        } else {
            
            if (preg_match('/[#$%*@,=+^]/', $this->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
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
        return ItemType::SERVICE_ITEM_TYPE;
    }
  
}