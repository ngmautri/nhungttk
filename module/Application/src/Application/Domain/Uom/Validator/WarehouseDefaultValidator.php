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
class WarehouseDefaultValidator extends AbstractValidator implements WarehouseValidatorInterface
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
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getWhName())) {
                $rootEntity->addError("WH name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getWhName()) == 1) {
                    $err = "Warehouse Name contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            if ($spec->isSatisfiedBy($rootEntity->getWhCode())) {
                $rootEntity->addError("WH code is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getWhCode()) == 1) {
                    $err = "Warehouse Code contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            // User
            $spec = $this->getSharedSpecificationFactory()->getUserExitsSpecification();

            // Controller
            if ($rootEntity->getWhController() > 0) {

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getWhController()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("Controller can not be identified. #" . $rootEntity->getWhController());
                }
            }

            // Created by
            if ($rootEntity->getCreatedBy() > 0) {

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getCreatedBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(\sprintf("User can not be identified. #%s, %s ", $rootEntity->getCreatedBy(), __CLASS__));
                }
            }

            // Last Change by
            if ($rootEntity->getLastChangeBy() > 0) {

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastChangeBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("User can not be identified. #" . $rootEntity->getLastChangeBy());
                }
            }

            // User
            $spec = $this->getSharedSpecificationFactory()->getEmailSpecification();
            if (! $rootEntity->getWhEmail() == null) {
                $spec = $rootEntity->getSharedSpecificationFactory()->getEmailSpecification();
                if (! $spec->isSatisfiedBy($rootEntity->getWhEmail())) {
                    $rootEntity->addError("Email invalid of Warehouse controller" . $rootEntity->getWhEmail());
                }
            }
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}