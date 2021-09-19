<?php
namespace Procure\Domain\PurchaseRequest\Validator;

use Procure\Domain\PurchaseRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\PurchaseRequest\Validator\Row\DefaultRowValidator;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\ValidationServiceImpl;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Webmozart\Assert\Assert;

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
     * @return \Procure\Domain\Service\ValidationServiceImpl
     */
    public static function create(SharedService $sharedService, $isPosting = false)
    {
        Assert::notNull($sharedService, "SharedService service not found");
        Assert::notNull($sharedService->getSharedSpecificationFactory(), "Shared spec service not found");

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();
        // $domainSpecsFactory = $sharedService->getDomainSpecificationFactory();

        $headerValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }
}