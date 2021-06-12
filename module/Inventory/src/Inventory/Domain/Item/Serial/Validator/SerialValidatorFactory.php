<?php
namespace Inventory\Domain\Item\Serial\Validator;

use Application\Domain\Service\Contracts\SharedServiceInterface;
use Inventory\Domain\Item\Variant\Validator\VariantAttributeDefaultValidator;
use Inventory\Domain\Item\Variant\Validator\VariantDefaultValidator;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantAttributeValidatorCollection;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorCollection;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\VariantValidationService;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SerialValidatorFactory
{

    public static function forCreatingVariant(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $variantValidators = new VariantValidatorCollection();
        $validator = new VariantDefaultValidator($sharedSpecsFactory);
        $variantValidators->add($validator);

        $variantAttribueValidators = null;
        Assert::notNull($variantValidators, "Variant Default Validator is null");

        return new VariantValidationService($variantValidators, $variantAttribueValidators);
    }

    /**
     *
     * @param SharedServiceInterface $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Service\VariantValidationService
     */
    public static function forCreatingVariantAttribute(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $variantValidators = new VariantValidatorCollection();
        $validator = new VariantDefaultValidator($sharedSpecsFactory);
        $variantValidators->add($validator);

        $variantAttributeValidators = new VariantAttributeValidatorCollection();
        $validator = new VariantAttributeDefaultValidator($sharedSpecsFactory);
        $variantAttributeValidators->add($validator);

        Assert::notNull($variantValidators, "Variant validator is null");
        Assert::notNull($variantAttributeValidators, "Variant attribute validator is null");

        return new VariantValidationService($variantValidators, $variantAttributeValidators);
    }
}