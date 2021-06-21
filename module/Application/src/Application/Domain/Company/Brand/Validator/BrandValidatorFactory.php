<?php
namespace Application\Domain\Company\Brand\Validator;

use Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorCollection;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandValidatorFactory
{

    /**
     *
     * @param SharedServiceInterface $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Application\Domain\Service\ItemAttributeValidationService
     */
    public static function forCreatingBrand(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $validators = new BrandValidatorCollection();
        $v = new BrandDefaultValidator($sharedSpecsFactory);
        $validators->add($v);

        return $validators;
    }
}