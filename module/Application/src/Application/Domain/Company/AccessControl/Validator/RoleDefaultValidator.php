<?php
namespace Application\Domain\Company\AccessControl\Validator;

use Application\Domain\Company\AccessControl\BaseRole;
use Application\Domain\Company\AccessControl\Validator\Contracts\RoleValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RoleDefaultValidator extends AbstractValidator implements RoleValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccessControl\Validator\Contracts\RoleValidatorInterface::validate()
     */
    public function validate(BaseRole $rootEntity)
    {
        if (! $rootEntity instanceof BaseRole) {
            $rootEntity->addError("BaseRole object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}