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

    const COMPOSITE_ITEM = "5";

    public static function getSupportedType()
    {
        $r = [];
        $r[] = self::INVENTORY_ITEM_TYPE;
        $r[] = self::NONE_INVENTORY_ITEM_TYPE;
        $r[] = self::SERVICE_ITEM_TYPE;
        $r[] = self::FIXED_ASSET_ITEM_TYPE;
        $r[] = self::COMPOSITE_ITEM;
        return $r;
    }

    public static function isSupported($typeId)

    {
        $r = [];
        $r[] = self::INVENTORY_ITEM_TYPE;
        $r[] = self::NONE_INVENTORY_ITEM_TYPE;
        $r[] = self::SERVICE_ITEM_TYPE;
        $r[] = self::FIXED_ASSET_ITEM_TYPE;
        $r[] = self::COMPOSITE_ITEM;
        return $r;
    }

    public static function getSupportedTypeArray()
    {
        $r = [];
        $r[self::INVENTORY_ITEM_TYPE] = "Inventory Item";
        $r[self::NONE_INVENTORY_ITEM_TYPE] = "None-Inventory Item";
        $r[self::SERVICE_ITEM_TYPE] = "Service";
        $r[self::FIXED_ASSET_ITEM_TYPE] = "Fixed Asset";
        $r[self::COMPOSITE_ITEM] = "Combo-Item";
        return $r;
    }
}