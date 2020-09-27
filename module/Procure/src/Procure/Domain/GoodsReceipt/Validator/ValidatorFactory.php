<?php
namespace Procure\Domain\GoodsReceipt\Validator;

use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrPostingValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\ReversalValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\GLAccountValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\WarehouseValidator;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\ValidationServiceImpl;
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
     * @param string $docType
     * @param SharedService $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\Service\ValidationServiceImpl
     */
    public static function create($docType, SharedService $sharedService, $isPosting = false)
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

        $defaultHeaderValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $defaultHeaderValidators->add($validator);

        $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $defaultHeaderValidators->add($validator);

        // ================
        $defaultRowValidators = new RowValidatorCollection();

        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        $validator = new PoRowValidator($sharedSpecsFactory, $fxService, $domainSpecsFactory);
        $defaultRowValidators->add($validator);

        $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        switch ($docType) {

            case ProcureDocType::GR: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_FROM_INVOICE: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;

                $validator = new ReversalValidator($sharedSpecsFactory, $fxService);
                $headerValidators->add($validator);

                break;

            case ProcureDocType::GOODS_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_FROM_WH_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;
        }

        if ($isPosting) {
            $validator = new GrPostingValidator($sharedSpecsFactory, $fxService);
            $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
            $headerValidators->add($validator);
        }

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }

    /**
     *
     * @param string $docType
     * @param SharedService $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\Service\ValidationServiceImpl
     */
    public static function createForPosting($docType, SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $fxService = $sharedService->getFxService();

        $sharedSpecFactory = $sharedService->getSharedSpecificationFactory();

        $defaultHeaderValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
        $defaultHeaderValidators->add($validator);
        $validator = new GrDateAndWarehouseValidator($sharedSpecFactory, $fxService);
        $defaultHeaderValidators->add($validator);
        $validator = new GrPostingValidator($sharedSpecFactory, $fxService);
        $defaultHeaderValidators->add($validator);

        $defaultRowValidators = new RowValidatorCollection();

        $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
        $defaultRowValidators->add($validator);
        $validator = new GLAccountValidator($sharedSpecFactory, $fxService);

        switch ($docType) {

            case ProcureDocType::GR: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_FROM_INVOICE: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_REVERSAL_FROM_AP_RESERVAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_FROM_WH_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;
        }

        if ($isPosting) {
            $validator = new GrPostingValidator($sharedSpecFactory, $fxService);
            $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
            $headerValidators->add($validator);
        }

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }

    /**
     *
     * @param string $docType
     * @param SharedService $sharedService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\Service\ValidationServiceImpl
     */
    public static function createForCopyFromAP($docType, SharedService $sharedService, $isPosting = false)
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

        // HEADER
        $defaultHeaderValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $defaultHeaderValidators->add($validator);

        $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $defaultHeaderValidators->add($validator);

        // ROW
        $defaultRowValidators = new RowValidatorCollection();

        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        $validator = new GLAccountValidator($sharedSpecsFactory, $fxService);
        $defaultRowValidators->add($validator);

        switch ($docType) {

            case ProcureDocType::GR: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_FROM_INVOICE: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GR_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $validator = new ReversalValidator($sharedSpecsFactory, $fxService);
                $headerValidators->add($validator);

                $rowValidators = $defaultRowValidators;

                break;

            case ProcureDocType::GOODS_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_FROM_WH_RETURN: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;

            case ProcureDocType::GOODS_RETURN_REVERSAL: // manual
                $headerValidators = $defaultHeaderValidators;
                $rowValidators = $defaultRowValidators;
                break;
        }

        if ($isPosting) {
            $validator = new GrPostingValidator($sharedSpecsFactory, $fxService);
            $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
            $headerValidators->add($validator);
        }

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }
}