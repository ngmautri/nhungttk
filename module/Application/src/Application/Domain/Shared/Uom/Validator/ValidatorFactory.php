<?php
namespace Inventory\Domain\Warehouse\Validator;

use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\WhValidationService;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ValidatorFactory
{

    const CREATE_NEW_WH = '1';

    const EDIT_WH = '2';

    const CREATE_NEW_LOCATION = '3';

    const EDIT_LOCATION = '4';

    public static function create(SharedService $sharedService, $context)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        // Default Warehouse Validator:
        $defaultWarehouseValidators = new WarehouseValidatorCollection();

        $validator = new WarehouseDefaultValidator($sharedSpecsFactory);
        $defaultWarehouseValidators->add($validator);
        $validator = new WarehouseDefaultLocationValidator($sharedSpecsFactory);
        $defaultWarehouseValidators->add($validator);

        // Default Location Validator:
        $defaultLocationValidators = new LocationValidatorCollection();
        $validator = new LocationDefaultValidator($sharedSpecsFactory);
        $defaultLocationValidators->add($validator);

        $warehouseValidators = $defaultWarehouseValidators;
        $locationValidators = $defaultLocationValidators;

        switch ($context) {
            case self::CREATE_NEW_WH:
                $validator = new WarehouseCodeValidator($sharedSpecsFactory);
                $warehouseValidators->add($validator);
                break;

            case self::EDIT_WH:
                break;

            case self::CREATE_NEW_LOCATION:
                break;

            case self::EDIT_LOCATION:
                break;
        }

        return new WhValidationService($warehouseValidators, $locationValidators);
    }
}