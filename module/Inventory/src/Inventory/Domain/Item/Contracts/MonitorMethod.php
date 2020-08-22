<?php
namespace Inventory\Domain\Item\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class MonitorMethod
{

    const ITEM_WITH_SERIAL_NO = 'SN';

    const ITEM_WITH_BATCH_NO = 'B';

    public static function getSupportedMethod()
    {
        $r = [];
        $r[] = self::ITEM_WITH_SERIAL_NO;
        $r[] = self::ITEM_WITH_BATCH_NO;
        return $r;
    }

    public static function getSupportedMethodArray()
    {
        $r = [];
        $r[self::ITEM_WITH_SERIAL_NO] = "Serial Number";
        $r[self::ITEM_WITH_BATCH_NO] = "Batch Numer";
        return $r;
    }
}