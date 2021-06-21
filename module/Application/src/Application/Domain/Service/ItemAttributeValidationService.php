<?php
namespace Application\Domain\Service;

use InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeValidationService implements ItemAttributeValidationServiceInterface
{

    private $attributeGroupValidators;

    private $attributeValidators;

    public function $validators(ItemAttributeGroupValidatorCollection $attributeGroupValidators, ItemAttributeValidatorCollection $attributeValidators = null)
    {
    if ($attributeGroupValidators == null) {
        throw new InvalidArgumentException("Att Group Validator(s) is required");
    }

    $this->attributeGroupValidators = $attributeGroupValidators;
    $this->attributeValidators = $attributeValidators;
}

    public function getAttributeGroupValidators()
    {
        return $this->attributeGroupValidators;
    }

    public function getAttributeValidators()
    {
        return $this->attributeValidators;
    }
}