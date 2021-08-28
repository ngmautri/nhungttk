<?php
namespace Inventory\Domain\Item\Component\Validator;

use Inventory\Domain\Item\Component\Validator\Contracts\ComponentValidatorCollection;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Item\Validator\ValidatorFactory;
use Inventory\Domain\Item\Variant\Validator\VariantAttributeDefaultValidator;
use Inventory\Domain\Service\DefaultItemComponenttValidationService;
use Inventory\Domain\Service\SharedService;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemComponentValidatorFactory
{

    public static function forCreatingItemComposite(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        $itemValidators = ValidatorFactory::create(ItemType::COMPOSITE_ITEM, $sharedService);
        return new DefaultItemComponenttValidationService($itemValidators);
    }

    public static function forCreatingComponent(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $itemValidators = ValidatorFactory::create(ItemType::COMPOSITE_ITEM, $sharedService);
        $componentValidators = new ComponentValidatorCollection();

        $validator = new VariantAttributeDefaultValidator($sharedSpecsFactory);
        $componentValidators->add($validator);

        Assert::notNull($componentValidators, "item component validator is null");

        return new DefaultItemComponenttValidationService($itemValidators, $componentValidators);
    }
}