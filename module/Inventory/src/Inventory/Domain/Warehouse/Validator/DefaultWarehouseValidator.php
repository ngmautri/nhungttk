<?php
namespace Inventory\Domain\Warehouse\Validator;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultWarehouseValidator implements WarehouseValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseWarehouse $rootEntity)
    {}
}