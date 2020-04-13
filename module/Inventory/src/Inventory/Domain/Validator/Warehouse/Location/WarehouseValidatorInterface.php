<?php
namespace Inventory\Domain\Validator\Warehouse;

use Inventory\Domain\Warehouse\AbstractWarehouse;
use Procure\Domain\AbstractDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface WarehouseValidatorInterface
{
    /**
     * 
     * @param AbstractDoc $rootEntity
     */
    public function validate(AbstractWarehouse $rootEntity);
}

