<?php
namespace Application\Domain\Company\Validator;

use Application\Domain\Company\Department\BaseDepartment;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Application\Domain\Company\Validator\Contracts\DepartmentValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentDefaultValidator extends AbstractValidator implements DepartmentValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseDepartment $rootEntity)
    {
        if (! $rootEntity instanceof BaseDepartment) {
            $rootEntity->addError("BaseDepartment object not found");
            return;
        }

        try {
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getDepartmentName())) {
                $rootEntity->addError("Department name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getDepartmentName()) == 1) {
                    $err = "Department Name contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            /*
             * if ($spec->isSatisfiedBy($rootEntity->getDepartmentCode())) {
             * $rootEntity->addError("Department code is null or empty.");
             * } else {
             *
             * if (preg_match('/[#$%@=+^]/', $rootEntity->getDepartmentCode()) == 1) {
             * $err = "Company Code contains invalid character (e.g. #,%,&,*)";
             * $rootEntity->addError($err);
             * }
             * }
             */

            // User
            $spec = $this->getSharedSpecificationFactory()->getUserExitsSpecification();

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
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}