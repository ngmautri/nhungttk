<?php
namespace User\Domain\User\Validator;

use Inventory\Domain\Warehouse\Validator\Contracts\AbstractValidator;
use User\Domain\User\BaseUser;
use User\Domain\User\Validator\Contracts\UserValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserDefaultValidator extends AbstractValidator implements UserValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseUser $rootEntity)
    {
        if (! $rootEntity instanceof BaseUser) {
            $rootEntity->addError("User object not found");
            return;
        }

        Try {
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getFirstname())) {
                $rootEntity->addError("First name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getFirstname()) == 1) {
                    $err = "First Name contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            if ($spec->isSatisfiedBy($rootEntity->getLastname())) {
                $rootEntity->addError("First name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getLastname()) == 1) {
                    $err = "First Name contains invalid character (e.g. #,%,&,*)";
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
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}