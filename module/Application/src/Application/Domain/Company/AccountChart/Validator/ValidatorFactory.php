<?php
namespace Application\Domain\Company\AccountChart\Validator;

use Application\Domain\Service\AccountChartValidationService;
use Inventory\Domain\Service\SharedService;
use Webmozart\Assert\Assert;
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
     * @param int $trxTypeId
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Service\TrxValidationService
     */
    public static function create(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $rowValidators = null;
        $headerValidators = null;

        Assert::notNull($headerValidators, "Trx header validator is null");
        Assert::notNull($rowValidators, "Trx row validator is null");

        return new AccountChartValidationService($headerValidators);
    }
}