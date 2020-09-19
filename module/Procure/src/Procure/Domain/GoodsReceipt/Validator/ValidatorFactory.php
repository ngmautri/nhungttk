<?php
namespace Procure\Domain\GoodsReceipt\Validator;

use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\Validator\Header\TrxPostingValidator;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Service\ValidationServiceImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ValidatorFactory
{

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

        $defaultRowValidators = null;
        $defaultHeaderValidators = null;

        switch ($docType) {

            case ProcureDocType::GR: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;

            case ProcureDocType::GR_FROM_INVOICE: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;

            case ProcureDocType::GR_REVERSAL: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;

            case ProcureDocType::RETURN: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;

            case ProcureDocType::RETURN_FROM_WH_RETURN: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;

            case ProcureDocType::RETURN_REVERSAL: // manual
                $headerValidators = $defaultRowValidators;
                $rowValidators = $defaultHeaderValidators;
                break;
        }

        if ($isPosting) {
            $validator = new TrxPostingValidator($sharedSpecsFactory, $fxService);
            $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
            $headerValidators->add($validator);
        }

        return new ValidationServiceImpl($headerValidators, $rowValidators);
    }
}