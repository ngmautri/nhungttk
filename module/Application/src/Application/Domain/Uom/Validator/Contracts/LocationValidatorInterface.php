<?php
namespace Inventory\Domain\Warehouse\Validator\Contracts;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface LocationValidatorInterface
{

    public function validate(BaseWarehouse $rootEntity, BaseLocation $localEntity);
}

