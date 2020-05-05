<?php
namespace Inventory\Domain\Item\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class ItemType
{

    const UNKNOWN_ITEM_TYPE = "0";

    const INVENTORY_ITEM_TYPE = "1";

    const NONE_INVENTORY_ITEM_TYPE = "2";

    const SERVICE_ITEM_TYPE = "3";

    const FIXED_ASSET_ITEM_TYPE = "4";
}