<?php
namespace Inventory\Domain\Warehouse\Repository;

use Application\Domain\Company\BaseCompany;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface WhCmdRepositoryInterface
{

    public function storeWholeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function RemoveWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false);

    public function removeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false);
}
