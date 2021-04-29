<?php
namespace Inventory\Domain\Warehouse\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultLocation
{

    const ROOT_LOCATION = 'ROOT_LOC';

    const SCRAP_LOCATION = 'SCRAP-LOC';

    const RETURN_LOCATION = 'RETURN-LOC';

    const RECYCLE_LOCATION = 'RECYCLE-BIN';

    static public function get()
    {
        return [
            self::ROOT_LOCATION,
            self::RETURN_LOCATION,
            self::SCRAP_LOCATION,
            self::RECYCLE_LOCATION
        ];
    }
}