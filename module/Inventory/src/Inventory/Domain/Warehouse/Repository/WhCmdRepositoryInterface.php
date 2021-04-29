<?php
namespace Inventory\Domain\Warehouse\Repository;

use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface WhCmdRepositoryInterface
{

    public function storeWarehouse(GenericWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeLocation(GenericWarehouse $rootEntity, GenericLocation $localEntity, $isPosting = false);

    public function removeLocation(GenericWarehouse $rootEntity, GenericLocation $localEntity, $isPosting = false);
}
