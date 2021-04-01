<?php
namespace Application\Domain\Company\Validator;

use Application\Domain\Company\Validator\Contracts\CompanyValidatorCollection;
use Application\Domain\Company\Validator\Contracts\DepartmentValidatorCollection;
use Application\Domain\Service\SharedService;
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

    /**
     *
     * @param SharedService $sharedService
     * @param string $context
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\Validator\Contracts\DepartmentValidatorCollection
     */
    public static function createForDepartment(SharedService $sharedService, $context)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        // Default Company Validator:
        $defaultValidators = new DepartmentValidatorCollection();

        $validator = new DepartmentDefaultValidator($sharedSpecsFactory);
        $defaultValidators->add($validator);

        return $defaultValidators;
    }
}