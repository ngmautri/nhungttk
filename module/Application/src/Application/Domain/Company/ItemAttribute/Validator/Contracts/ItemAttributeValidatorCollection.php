<?php
namespace Application\Domain\Company\ItemAttribute\Validator\Contracts;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeValidatorCollection implements ItemAttributeValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ItemAttributeValidatorInterface $validator)
    {
        if (! $validator instanceof ItemAttributeValidatorInterface) {
            throw new InvalidArgumentException("ItemAttributeValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    public function validate(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity, $localEntity);
        }
    }
}

