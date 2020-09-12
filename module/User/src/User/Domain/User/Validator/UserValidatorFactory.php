<?php
namespace Inventory\Domain\User\Validator;

use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Warehouse\Validator\WarehouseCodeValidator;
use User\Domain\User\Validator\UserDefaultValidator;
use User\Domain\User\Validator\Contracts\UserValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserValidatorFactory
{

    const CREATE_NEW_USER = '1';

    const EDIT_USER = '2';

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
        $defaultValidators = new UserValidatorCollection();

        $validator = new UserDefaultValidator($sharedSpecsFactory);
        $defaultValidators->add($validator);

        switch ($context) {
            case self::CREATE_NEW_WH:
                $validator = new WarehouseCodeValidator($sharedSpecsFactory);
                $defaultValidators->add($validator);
                break;

            case self::EDIT_WH:
                break;

            case self::CREATE_NEW_LOCATION:
                break;

            case self::EDIT_LOCATION:
                break;
        }

        return $defaultValidators;
    }
}