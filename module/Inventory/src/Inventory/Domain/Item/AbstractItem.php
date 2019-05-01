<?php
namespace Inventory\Domain\Item;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractItem{
    
    
    /**
     * 
     * @return string
     */
    public function getItemType()
    {
        return ItemType::UNKNOWN_ITEM_TYPE;
    }

   
}