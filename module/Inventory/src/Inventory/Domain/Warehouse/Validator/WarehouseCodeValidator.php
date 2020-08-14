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
class WarehouseCodeValidator extends AbstractValidator implements WarehouseValidatorInterface
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

            $spec = $this->getSharedSpecificationFactory()->getWarehouseCodeExitsSpecification();
            $subject = [
                "companyId" => $rootEntity->getCompany(),
                "whCode" => $rootEntity->getWhCode()
            ];

            if ($spec->isSatisfiedBy($subject)) {
                $rootEntity->addError("Warehouse Code exits! #" . $rootEntity->getWhCode());
            }
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}