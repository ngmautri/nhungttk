<?php
namespace Application\Domain\Company\Brand\Validator;

use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandDefaultValidator extends AbstractValidator implements BrandValidatorInterface
{

    public function validate(BaseBrand $rootEntity)
    {
        if (! $rootEntity instanceof BaseBrand) {
            $rootEntity->addError("BaseBrand object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}