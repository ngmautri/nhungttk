<?php
namespace Application\Domain\Warehouse\Validator;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Warehouse\Validator\Contracts\CompanyValidatorInterface;
use Inventory\Domain\Warehouse\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyDefaultValidator extends AbstractValidator implements CompanyValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseCompany $rootEntity)
    {
        if (! $rootEntity instanceof BaseCompany) {
            $rootEntity->addError("BaseCompany object not found");
            return;
        }

        try {
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getCompanyName())) {
                $rootEntity->addError("WH name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getCompanyName()) == 1) {
                    $err = "Company Name contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            if ($spec->isSatisfiedBy($rootEntity->getCompanyCode())) {
                $rootEntity->addError("WH code is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getCompanyCode()) == 1) {
                    $err = "Company Code contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

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