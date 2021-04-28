<?php
namespace Application\Domain\Company\AccountChart\Validator;

use Application\Domain\Company\AccountChart\Validator\Contracts\AccountValidatorCollection;
use Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorCollection;
use Application\Domain\Service\AccountChartValidationService;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartValidatorFactory
{

    /**
     *
     * @param SharedServiceInterface $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Application\Domain\Service\AccountChartValidationService
     */
    public static function forCreatingChart(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $chartValidators = new ChartValidatorCollection();
        $validator = new ChartDefaultValidator($sharedSpecsFactory);
        $chartValidators->add($validator);

        $accountValidators = null;
        Assert::notNull($chartValidators, "Chart validator is null");

        return new AccountChartValidationService($chartValidators, $accountValidators);
    }

    public static function forCreatingAccount(SharedServiceInterface $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $chartValidators = new ChartValidatorCollection();
        $validator = new ChartDefaultValidator($sharedSpecsFactory);
        $chartValidators->add($validator);

        $accountValidators = new AccountValidatorCollection();
        $validator = new AccountDefaultValidator($sharedSpecsFactory);

        $accountValidators->add($validator);

        Assert::notNull($chartValidators, "Chart validator is null");
        Assert::notNull($accountValidators, "Account validator is null");

        return new AccountChartValidationService($chartValidators, $accountValidators);
    }
}