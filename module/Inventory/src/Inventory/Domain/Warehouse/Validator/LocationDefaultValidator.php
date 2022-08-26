<?php
namespace Inventory\Domain\Warehouse\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Contracts\DefaultLocation;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationDefaultValidator extends AbstractValidator implements LocationValidatorInterface
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

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // company
            $spec = $this->getSharedSpecificationFactory()->getWarehouseExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "warehouseId" => $rootEntity->getId()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError("Warehouse not exits. #" . $rootEntity->getId());
            }

            // Check Parent Location
            $spec = $this->getSharedSpecificationFactory()->getWarehouseLocationExitsSpecification();

            $subject = array(
                "warehouseId" => $rootEntity->getId(),
                "locationId" => $localEntity->getParentId()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError("Parent Location not exits. #" . $localEntity->getParentId() . "WH#" . $rootEntity->getId());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($localEntity->getLocationName())) {
                $localEntity->addError("location name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $localEntity->getLocationName()) == 1) {
                    $err = "Location Name contains invalid character (e.g. #,%,&,*)";
                    $localEntity->addError($err);
                }

                if (in_array($localEntity->getLocationName(), DefaultLocation::get())) {
                    $err = sprintf("Location Name is reserved (%s)", $localEntity->getLocationName());
                    $localEntity->addError($err);
                }
            }

            $spec = $this->getSharedSpecificationFactory()->getCompanyUserExSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $localEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError("User is not identified for this transaction. #" . $localEntity->getCreatedBy());
            }
        } catch (Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}