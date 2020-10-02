<?php
namespace Inventory\Domain\Warehouse\Validator;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationCodeValidator extends AbstractValidator implements LocationValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorInterface::validate()
     */
    public function validate(BaseWarehouse $rootEntity, BaseLocation $localEntity)
    {
        if (! $rootEntity instanceof BaseWarehouse) {
            $localEntity->addError("Warehouse object not found");
            return;
        }

        if (! $localEntity instanceof BaseLocation) {
            $localEntity->addError("Location object not found");
            return;
        }
        try {
            // Todo;
        } catch (Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}