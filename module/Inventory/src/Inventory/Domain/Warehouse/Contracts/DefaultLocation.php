<?php
namespace Inventory\Domain\Warehouse\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultLocation
{

    const ROOT_LOCATION = 'ROOT-LOCATION';

    const SCRAP_LOCATION = 'SCRAP-LOCATION';

    const RETURN_LOCATION = 'RETURN-LOCATION';

    const RECYCLE_LOCATION = 'RECYCLE-LOCATION';

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