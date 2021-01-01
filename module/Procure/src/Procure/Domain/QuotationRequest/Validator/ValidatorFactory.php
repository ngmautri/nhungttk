<?php
namespace Procure\Domain\QuotationRequest\Validator;

use Procure\Domain\QuotationRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\QuotationRequest\Validator\Row\DefaultRowValidator;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\ValidationServiceImpl;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
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
     * @param SharedService $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\Service\ValidationServiceImpl
     */
    public static function create(SharedService $sharedService, $isPosting = false)
    {
        Assert::notNull($sharedService, "SharedService not found");

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();
        $domainSpecsFactory = $sharedService->getDomainSpecificationFactory();

        $headerValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }
}