<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantAttributeValidatorCollection implements VariantAttributeValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(VariantAttributeValidatorInterface $validator)
    {
        if (! $validator instanceof VariantAttributeValidatorInterface) {
            throw new InvalidArgumentException("VariantAttributeValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\VariantAttributeValidatorInterface::validate()
     */
    public function validate(BaseVariant $rootEntity, BaseVariantAttribute $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity, $localEntity);
        }
    }
}

