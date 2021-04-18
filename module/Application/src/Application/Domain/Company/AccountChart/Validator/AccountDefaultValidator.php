<?php
namespace Application\Domain\Company\AccountChart\Validator;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\Validator\Contracts\AccountValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountDefaultValidator extends AbstractValidator implements AccountValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorInterface::validate()
     */
    public function validate(BaseAccount $rootEntity)
    {
        if (! $rootEntity instanceof BaseAccount) {
            $rootEntity->addError("BaseAccount object not found");
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