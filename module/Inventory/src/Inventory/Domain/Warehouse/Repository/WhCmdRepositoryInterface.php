<?php
namespace Inventory\Domain\Warehouse\Repository;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface WhCmdRepositoryInterface
{

    public function storeWarehouse(BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false);

    public function removeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false);
}
