<?php
namespace Application\Domain\Company\ItemAttribute\Validator;

use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeGroupValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttributeGroupDefaultValidator extends AbstractValidator implements ItemAttributeGroupValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeGroupValidatorInterface::validate()
     */
    public function validate(BaseAttributeGroup $rootEntity)
    {
        if (! $rootEntity instanceof BaseAttributeGroup) {
            $rootEntity->addError("BaseAttributeGroup object not found");
            return;
        }

        try {
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getGroupCode())) {
                $rootEntity->addError("Attribute code is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getGroupCode()) == 1) {
                    $err = "Attribute code contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            if ($spec->isSatisfiedBy($rootEntity->getGroupName())) {
                $rootEntity->addError("Attribute name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getGroupName()) == 1) {
                    $err = "Attribute name contains invalid character (e.g. #,%,&,*)";
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