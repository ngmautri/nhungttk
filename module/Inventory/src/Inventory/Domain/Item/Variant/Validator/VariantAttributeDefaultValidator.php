<?php
namespace Inventory\Domain\Item\Variant\Validator;

use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantAttributeValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantAttributeDefaultValidator extends AbstractValidator implements VariantAttributeValidatorInterface
{

    /*
     *
     */
    public function validate(BaseVariant $rootEntity, BaseVariantAttribute $localEntity)
    {
        if (! $rootEntity instanceof BaseVariant) {
            $rootEntity->addError("BaseAttributeGroup object not found");
            return;
        }

        if (! $localEntity instanceof BaseVariantAttribute) {
            $rootEntity->addError("BaseAttribute object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}