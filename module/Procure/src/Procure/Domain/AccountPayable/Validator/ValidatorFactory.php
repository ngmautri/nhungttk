<?php
namespace Procure\Domain\AccountPayable\Validator;

use Procure\Domain\AccountPayable\Validator\Header\APPostingValidator;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\AccountPayable\Validator\Header\IncotermValidator;
use Procure\Domain\AccountPayable\Validator\Header\InvoiceAndPaymentTermValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\GLAccountValidator;
use Procure\Domain\AccountPayable\Validator\Row\PoRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\PrRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\WarehouseValidator;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\ValidationServiceImp;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
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

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();
        $domainSpecsFactory = $sharedService->getDomainSpecificationFactory();

        $rowValidators = null;
        $headerValidators = null;

        $headerValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $validator = new PrRowValidator($sharedSpecsFactory, $fxService, $domainSpecsFactory);
        $rowValidators->add($validator);

        $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        if ($isPosting) {
            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new APPostingValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
        }

        return new ValidationServiceImp($headerValidators, $rowValidators);
    }

    public static function createForHeader(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $rowValidators = null;
        $headerValidators = null;
        $headerValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new IncotermValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new InvoiceAndPaymentTermValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new APPostingValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        return new ValidationServiceImp($headerValidators, $rowValidators);
    }

    public static function createForPosting(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $fxService = $sharedService->getFxService();

        $sharedSpecFactory = $sharedService->getSharedSpecificationFactory();
        $domainSpecFactory = $sharedService->getDomainSpecificationFactory();

        $headerValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new GrDateAndWarehouseValidator($sharedSpecFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new APPostingValidator($sharedSpecFactory, $fxService);
        $headerValidators->add($validator);

        // =======================

        $rowValidators = new RowValidatorCollection();

        $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
        $rowValidators->add($validator);

        $validator = new PoRowValidator($sharedSpecFactory, $fxService, $domainSpecFactory);
        $rowValidators->add($validator);

        $validator = new WarehouseValidator($sharedSpecFactory, $fxService);
        $rowValidators->add($validator);

        $validator = new GLAccountValidator($sharedSpecFactory, $fxService);
        $rowValidators->add($validator);

        return new ValidationServiceImp($headerValidators, $rowValidators);
    }

    public static function createForCopyFromPO(SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        // Header validator
        $headerValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new InvoiceAndPaymentTermValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        // Row validator
        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        return new ValidationServiceImp($headerValidators, $rowValidators);
    }
}