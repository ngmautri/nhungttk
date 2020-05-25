<?php
namespace Inventory\Domain\Item\Validator;

use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ValidatorFactory
{

    /**
     *
     * @param int $itemTypeId
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Validator\Item\ItemValidatorCollection
     */
    public static function create($itemTypeId, SharedService $sharedService)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $validatorCollection = new ItemValidatorCollection();
        $validator = new DefaultItemValidator($sharedSpecsFactory);
        $validatorCollection->add($validator);

        switch ($itemTypeId) {

            case ItemType::INVENTORY_ITEM_TYPE:

                $validator = new InventoryItemValidator($sharedSpecsFactory);
                $validatorCollection->add($validator);

                break;

            case ItemType::FIXED_ASSET_ITEM_TYPE:
                $validator = new FixedAssetValidator($sharedSpecsFactory);
                $validatorCollection->add($validator);
                break;
        }

        return $validatorCollection;
    }
}