<?php
namespace Inventory\Domain\Item\Variant\Validator;

use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorInterface;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantDefaultValidator extends AbstractValidator implements VariantValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorInterface::validate()
     */
    public function validate(BaseVariant $rootEntity)
    {
        if (! $rootEntity instanceof BaseVariant) {
            $rootEntity->addError("BaseAttributeGroup object not found");
            return;
        }

        try {
        /**
         *
         * @todo
         */
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}