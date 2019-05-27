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
    {
        $notification = new Notification();
        $notification->setSourceClass("nmt");
        
        if ($this->isNullOrBlank($this->getCreatedBy())) {
            $err = "User ID is not found.";
            $notification->addError($err);
        }
        
        if ($this->isNullOrBlank($this->getItemName())) {
            $err = "Item name is null or empty. It is required for any item.";
            $notification->addError($err);
        } else {
            
            if (preg_match('/[#$%*@,=+^]/', $this->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
        }
        
        if (!$this->isNullOrBlank($this->getStockUom()) && $this->isNullOrBlank($this->getStockUomConvertFactor())) {
            $err = "Inventory measurement unit is set, but no conversion factor!";
            $notification->addError($err);
        }
        
        if (!$this->isNullOrBlank($this->getPurchaseUom()) && $this->isNullOrBlank($this->getPurchaseUomConvertFactor())) {
            $err = "Purchase measurement unit is set, but no conversion factor!";
            $notification->addError($err);
        }
        
        if (!$this->isNullOrBlank($this->getSalesUom()) && $this->isNullOrBlank($this->getSalesUomConvertFactor())) {
            $err = "Sales measurement unit is set, but no conversion factor!";
            $notification->addError($err);
        }
        
        return $notification;
    }

}