<?php
namespace Application\Domain\Company\Department\Validator;

use Application\Domain\Company\Department\Validator\Contracts\DepartmentValidatorCollection;
use Application\Domain\Service\SharedService;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentValidatorFactory
{

    /**
     *
     * @param SharedService $sharedService
     * @param string $context
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\Department\Validator\Contracts\DepartmentValidatorCollection
     */
    public static function create(SharedService $sharedService, $context = null)
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