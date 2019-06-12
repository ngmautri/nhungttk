<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\ItemType;
use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Domain\Item\NoneInventoryItem;
use Inventory\Domain\Item\FixedAssetItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemFactory
{

    public static function createItem($itemTypeId)
    {
        switch ($itemTypeId) {

            case ItemType::INVENTORY_ITEM_TYPE:
                $item = new InventoryItem();
                break;
                
                
            case ItemType::NONE_INVENTORY_ITEM_TYPE:
                $item = new NoneInventoryItem();
                break;

            case ItemType::SERVICE_ITEM_TYPE:
                $item = new ServiceItem();
                break;


            case ItemType::FIXED_ASSET_ITEM_TYPE:
                $item = new FixedAssetItem();
                break;
            default:
                $item = new InventoryItem();
                break;
        }

        return $item;
    }
}