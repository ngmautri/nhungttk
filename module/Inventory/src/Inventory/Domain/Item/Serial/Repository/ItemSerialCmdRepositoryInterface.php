<?php
namespace Inventory\Domain\Item\Serial\Repository;

use Inventory\Domain\Item\Serial\GenericSerial;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ItemSerialCmdRepositoryInterface
{

    public function storeSerial(GenericSerial $rootEntity, $generateSysNumber = True);

    public function storeSerialCollection($collection, $generateSysNumber = True);

    public function removeSerial(GenericSerial $rootEntity, $isPosting = false);
}
