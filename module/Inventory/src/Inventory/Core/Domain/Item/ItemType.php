<?php
namespace Inventory\Domain\Item;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class ItemType{
    
    Const UNKNOWN_ITEM_TYPE = "0";
    Const INVENTORY_ITEM_TYPE = "1";
    Const NONE_INVENTORY_ITEM_TYPE = "2";
    Const SERVICE_ITEM_TYPE = "3";
    Const FIXED_ASSET_ITEM_TYPE = "4";
}