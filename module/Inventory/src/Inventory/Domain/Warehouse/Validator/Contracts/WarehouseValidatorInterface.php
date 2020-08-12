<?php
namespace Inventory\Domain\Warehouse\Validator\Contracts;

use Inventory\Domain\Warehouse\BaseWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface WarehouseValidatorInterface
{

    public function validate(BaseWarehouse $rootEntity);
}

