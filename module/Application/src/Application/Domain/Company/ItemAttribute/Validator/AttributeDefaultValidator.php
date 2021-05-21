<?php
namespace Application\Domain\Company\ItemAttribute\Validator;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttributeDefaultValidator extends AbstractValidator implements ItemAttributeValidatorInterface
{

    /*
     *
     */
    public function validate(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity)
    {
        if (! $rootEntity instanceof BaseAttributeGroup) {
            $rootEntity->addError("BaseAttributeGroup object not found");
            return;
        }

        if (! $localEntity instanceof BaseAttribute) {
            $rootEntity->addError("BaseAttribute object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}