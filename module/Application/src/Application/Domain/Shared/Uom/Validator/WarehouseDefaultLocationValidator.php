<?php
namespace Inventory\Domain\Warehouse\Validator;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseDefaultLocationValidator extends AbstractValidator implements WarehouseValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseWarehouse $rootEntity)
    {
        if (! $rootEntity instanceof BaseWarehouse) {
            $rootEntity->addError("Warehouse object not found");
            return;
        }
        try {

            if ($rootEntity->getRootLocation() == null) {
                $rootEntity->addError("Can not find Root location");
            }

            if ($rootEntity->getRecycleLocation() == null) {
                $rootEntity->addError("Can not find recycle location");
            }

            if ($rootEntity->getReturnLocation() == null) {
                $rootEntity->addError("Can not find recycle location");
            }

            if ($rootEntity->getScrapLocation() == null) {
                $rootEntity->addError("Can not find Scrap location");
            }
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}