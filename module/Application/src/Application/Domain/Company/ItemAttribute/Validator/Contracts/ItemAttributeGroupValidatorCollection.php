<?php
namespace Application\Domain\Company\ItemAttribute\Validator\Contracts;

use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeGroupValidatorCollection implements ItemAttributeGroupValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ItemAttributeGroupValidatorInterface $validator)
    {
        if (! $validator instanceof ItemAttributeGroupValidatorInterface) {
            throw new InvalidArgumentException("ItemAttributeValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeGroupValidatorInterface::validate()
     */
    public function validate(BaseAttributeGroup $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}

