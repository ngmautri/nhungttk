<?php
namespace Application\Domain\Company\Validator;

use Application\Domain\Service\SharedService;
use Application\Domain\Warehouse\Validator\CompanyDefaultValidator;
use Application\Domain\Warehouse\Validator\Contracts\CompanyValidatorCollection;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ValidatorFactory
{

    public static function create(SharedService $sharedService, $context)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        // Default Company Validator:
        $defaultValidators = new CompanyValidatorCollection();

        $validator = new CompanyDefaultValidator($sharedSpecsFactory);
        $defaultValidators->add($validator);

        return $defaultValidators;
    }
}