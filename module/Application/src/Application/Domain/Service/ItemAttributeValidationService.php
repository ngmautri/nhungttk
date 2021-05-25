<?php
namespace Application\Domain\Service;

use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeGroupValidatorCollection;
use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeValidatorCollection;
use Application\Domain\Service\Contracts\ItemAttributeValidationServiceInterface;
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

    public function __construct(ItemAttributeGroupValidatorCollection $attributeGroupValidators, ItemAttributeValidatorCollection $attributeValidators = null)
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