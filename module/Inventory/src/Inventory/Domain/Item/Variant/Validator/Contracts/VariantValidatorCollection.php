<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Variant\BaseVariant;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantValidatorCollection implements VariantValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(VariantValidatorInterface $validator)
    {
        if (! $validator instanceof VariantValidatorInterface) {
            throw new InvalidArgumentException("VariantValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorInterface::validate()
     */
    public function validate(BaseVariant $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}

