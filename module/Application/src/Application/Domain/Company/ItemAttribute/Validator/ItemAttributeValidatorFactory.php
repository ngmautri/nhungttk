<?php
namespace Application\Domain\Company\ItemAttribute\Validator;

use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeGroupValidatorCollection;
use Application\Domain\Company\ItemAttribute\Validator\Contracts\ItemAttributeValidatorCollection;
use Application\Domain\Service\ItemAttributeValidationService;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeValidatorFactory
{

    /**
     *
     * @param SharedServiceInterface $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Application\Domain\Service\ItemAttributeValidationService
     */
    public static function forCreatingAttributeGroup(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $attributeGroupValidators = new ItemAttributeGroupValidatorCollection();
        $validator = new AttributeGroupDefaultValidator($sharedSpecsFactory);
        $attributeGroupValidators->add($validator);

        Assert::notNull($attributeGroupValidators, "Attribute Default Validator is null");

        return new ItemAttributeValidationService($attributeGroupValidators);
    }

    /**
     *
     * @param SharedServiceInterface $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Application\Domain\Service\ItemAttributeValidationService
     */
    public static function forCreatingAttribute(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $attributeGroupValidators = new ItemAttributeGroupValidatorCollection();
        $validator = new AttributeGroupDefaultValidator($sharedSpecsFactory);
        $attributeGroupValidators->add($validator);

        $attributeValidators = new ItemAttributeValidatorCollection();
        $validator = new AttributeDefaultValidator($sharedSpecsFactory);
        $attributeValidators->add($validator);

        Assert::notNull($attributeGroupValidators, "Attribute Group Validator is null");
        Assert::notNull($attributeValidators, "Attribute validator is null");

        return new ItemAttributeValidationService($attributeGroupValidators, $attributeValidators);
    }
}