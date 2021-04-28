<?php
namespace Application\Domain\Company\AccountChart\Validator;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
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
     * @see \Application\Domain\Company\AccountChart\Validator\Contracts\AccountValidatorInterface::validate()
     */
    public function validate(BaseChart $rootEntity, BaseAccount $localEntity)
    {
        if (! $rootEntity instanceof BaseChart) {
            $rootEntity->addError("BaseChart object not found");
            return;
        }

        if (! $localEntity instanceof BaseAccount) {
            $rootEntity->addError("BaseAccount object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}