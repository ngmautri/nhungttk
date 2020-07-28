<?php
namespace Inventory\Domain\Transaction\Validator;

use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxValidationService;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Transaction\Validator\Header\DefaultHeaderValidator;
use Inventory\Domain\Transaction\Validator\Row\DefaultRowValidator;
use Inventory\Domain\Transaction\Validator\Row\WarehouseValidator;
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
     * @param int $itemTypeId
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Validator\Item\ItemValidatorCollection
     */
    public static function create($trxTypeId, SharedService $sharedService)
    {
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($sharedService->getSharedSpecificationFactory() == null) {
            throw new InvalidArgumentException("Shared spec service not found");
        }

        $fxService = $sharedService->getFxService();

        $sharedSpecsFactory = $sharedService->getSharedSpecificationFactory();

        // Header validators
        $headerValidators = new HeaderValidatorCollection();
        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
        $headerValidators->add($validator);

        // Row validators
        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        switch ($trxTypeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:

                break;

            case TrxType::GR_FROM_PURCHASING:
                $validator = new WarehouseValidator($sharedSpecsFactory, $fxService);
                $rowValidators->add($validator);
                break;
        }

        return new TrxValidationService($headerValidators, $rowValidators);
    }
}