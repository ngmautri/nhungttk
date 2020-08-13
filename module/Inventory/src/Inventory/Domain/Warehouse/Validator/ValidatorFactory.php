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

    public static function create(SharedService $sharedService, $context = null)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $warehouseValidators = new WarehouseValidatorCollection();
        $locationValidators = new LocationValidatorCollection();

        return new WhValidationService($warehouseValidators, $locationValidators);
    }
}