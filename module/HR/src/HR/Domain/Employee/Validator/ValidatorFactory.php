<?php
namespace HR\Domain\Employee\Validator;

use Application\Domain\Contracts\ValidationContext;
use HR\Domain\Contracts\IndividualType;
use HR\Domain\Service\Contracts\SharedServiceInterface;
use HR\Domain\Validator\Employee\IndividualValidatorCollection;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ValidatorFactory
{

    public static function create($individualTypeId, SharedServiceInterface $sharedService, $isPosting = false, $context = null)
    {
        Assert::notNull($sharedService, "SharedService not found");

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();
        Assert::notNull($sharedSpecsFactory, "Shared spec not found");

        $domainSpecsFactory = $sharedService->getDomainSpecificationFactory();
        Assert::notNull($sharedSpecsFactory, "HR Domain Spec factory not found");

        $validatorCollection = new IndividualValidatorCollection();
        $validator = new DefaultIndividualValidator($sharedSpecsFactory);
        $validatorCollection->add($validator);

        if ($context = ValidationContext::CREATE) {
            $validator = new EmployeeCodeValidator($sharedSpecsFactory, $domainSpecsFactory);
            $validatorCollection->add($validator);
        }

        switch ($individualTypeId) {

            case IndividualType::APPLICANT:
                break;
            case IndividualType::EMPLOYEE:

                break;
        }

        return $validatorCollection;
    }
}