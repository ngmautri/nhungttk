<?php
namespace Inventory\Domain\Transaction\Validator;

use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxValidationService;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\GI\Validator\Header\DefaultGIHeaderValidator;
use Inventory\Domain\Transaction\GI\Validator\Row\CostCenterValidator;
use Inventory\Domain\Transaction\GI\Validator\Row\DefaultGIRowValidator;
use Inventory\Domain\Transaction\GI\Validator\Row\GIForMachineValidator;
use Inventory\Domain\Transaction\GI\Validator\Row\OnHandQuantityValidator;
use Inventory\Domain\Transaction\GR\Validator\Header\DefaultGRHeaderValidator;
use Inventory\Domain\Transaction\GR\Validator\Header\OpeningBalanceHeaderValidator;
use Inventory\Domain\Transaction\GR\Validator\Row\DefaultGRRowValidator;
use Inventory\Domain\Transaction\GR\Validator\Row\OpeningBalanceRowValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Transaction\Validator\Header\DefaultHeaderValidator;
use Inventory\Domain\Transaction\Validator\Header\TrxDateAndWarehouseValidator;
use Inventory\Domain\Transaction\Validator\Header\TrxPostingValidator;
use Inventory\Domain\Transaction\Validator\Header\WarehousePermissionValidator;
use Inventory\Domain\Transaction\Validator\Row\DefaultRowValidator;
use Inventory\Domain\Transaction\Validator\Row\NoneNegativePriceValidator;
use Inventory\Domain\Transaction\Validator\Row\NoneNegativeQuantityValidator;
use Inventory\Domain\Transaction\Validator\Row\PositiveQuantityValidator;
use InvalidArgumentException;
use Inventory\Application\Specification\Inventory\OnhandQuantityOfMovementSpecification;
use Inventory\Domain\Transaction\GI\Validator\Row\GIForReturnPOValidator;

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
    public static function create($trxTypeId, SharedService $sharedService, $isPosting = false)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        // echo ($trxTypeId);

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        $rowValidators = null;
        $headerValidators = null;

        // Goods Issue Validator
        // ===========================
        $giHeaderValidators = new HeaderValidatorCollection();

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $giHeaderValidators->add($validator);

        $validator = new DefaultGIHeaderValidator($sharedSpecsFactory, $fxService);
        $giHeaderValidators->add($validator);

        $validator = new TrxDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $giHeaderValidators->add($validator);

        $validator = new WarehousePermissionValidator($sharedSpecsFactory, $fxService);
        $giHeaderValidators->add($validator);

        // Goods issues row validators
        // ============================
        $giRowValidators = new RowValidatorCollection();

        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $giRowValidators->add($validator);

        $validator = new DefaultGIRowValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $giRowValidators->add($validator);

        $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService); // can not issue Zero Qty.
        $giRowValidators->add($validator);

        $validator = new OnHandQuantityValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $giRowValidators->add($validator);

        // Goods Receipt Validator
        // ==========================

        $grHeaderValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $grHeaderValidators->add($validator);

        $validator = new TrxDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
        $grHeaderValidators->add($validator);

        $validator = new DefaultGRHeaderValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $grHeaderValidators->add($validator);

        // Goods Receipt row validators
        // ============================
        $grRowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $grRowValidators->add($validator);

        $validator = new DefaultGRRowValidator($sharedSpecsFactory, $fxService);
        $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
        $grRowValidators->add($validator);

        switch ($trxTypeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $headerValidators = $grHeaderValidators;
                $validator = new OpeningBalanceHeaderValidator($sharedSpecsFactory, $fxService);
                $headerValidators->add($validator);
                $rowValidators = $grRowValidators;
                $validator = new OpeningBalanceRowValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                // add more,if needed
                break;

            case TrxType::GR_FROM_PURCHASING:
                $headerValidators = $grHeaderValidators;
                $rowValidators = $grRowValidators;
                $validator = new NoneNegativeQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            case TrxType::GR_FROM_EXCHANGE: // auto
                $headerValidators = $grHeaderValidators;

                $rowValidators = $grRowValidators;
                $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            case TrxType::GR_WITHOUT_INVOICE:
                $headerValidators = $grHeaderValidators;
                $validator = new WarehousePermissionValidator($sharedSpecsFactory, $fxService);
                $headerValidators->add($validator);

                $rowValidators = $grRowValidators;

                $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            case TrxType::GR_FROM_TRANSFER_LOCATION: // auto
                $headerValidators = $grHeaderValidators;
                $rowValidators = $grRowValidators;
                $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            case TrxType::GR_FROM_TRANSFER_WAREHOUSE: // auto
                $headerValidators = $grHeaderValidators;

                $rowValidators = $grRowValidators;
                $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            case TrxType::GR_FOR_ADJUSTMENT_AFTER_COUNTING: // manual
                $headerValidators = $grHeaderValidators;
                $validator = new WarehousePermissionValidator($sharedSpecsFactory, $fxService);
                $headerValidators->add($validator);

                $rowValidators = $grRowValidators;
                $validator = new PositiveQuantityValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                $validator = new NoneNegativePriceValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);

                break;

            // ==============

            case TrxType::GI_FOR_COST_CENTER:
                $headerValidators = $giHeaderValidators;

                $rowValidators = $giRowValidators;
                $validator = new CostCenterValidator($sharedSpecsFactory, $fxService);
                $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
                $rowValidators->add($validator);

                break;

            case TrxType::GI_FOR_REPAIR_MACHINE_WITH_EX:
                $headerValidators = $giHeaderValidators;

                $rowValidators = $giRowValidators;
                $validator = new GIForMachineValidator($sharedSpecsFactory, $fxService);
                $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
                $rowValidators->add($validator);
                break;

            case TrxType::GI_FOR_RETURN_PO:
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;
                $validator = new GIForReturnPOValidator($sharedSpecsFactory, $fxService);
                $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
                $rowValidators->add($validator);

                break;

            case TrxType::GI_FOR_REPAIR_MACHINE:
                $headerValidators = $giHeaderValidators;

                $rowValidators = $giRowValidators;
                $validator = new GIForMachineValidator($sharedSpecsFactory, $fxService);
                $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
                $rowValidators->add($validator);

                break;

            case TrxType::GI_FOR_TRANSFER_LOCATION:
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;

                break;

            case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;

                break;

            case TrxType::GI_FOR_DISPOSAL:
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;
                break;

            case TrxType::GI_FOR_PURCHASING_REVERSAL:
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;
                break;

            case TrxType::GI_FOR_ADJUSTMENT_AFTER_COUNTING: // manual
                $headerValidators = $giHeaderValidators;
                $rowValidators = $giRowValidators;
                break;
        }

        if ($isPosting) {
            $validator = new TrxPostingValidator($sharedSpecsFactory, $fxService);
            $validator->setDomainSpecificationFactory($sharedService->getDomainSpecificationFactory());
            $headerValidators->add($validator);
        }

        return new TrxValidationService($headerValidators, $rowValidators);
    }
}