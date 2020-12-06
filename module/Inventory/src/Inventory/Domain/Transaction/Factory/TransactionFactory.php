<?php
namespace Inventory\Domain\Transaction\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\TrxHeaderCreated;
use Inventory\Domain\Event\Transaction\TrxHeaderUpdated;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\TrxSnapshotAssembler;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\GI\GIFromGRReversal;
use Inventory\Domain\Transaction\GI\GIFromPurchasingReversal;
use Inventory\Domain\Transaction\GI\GIforAdjustment;
use Inventory\Domain\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Transaction\GI\GIforExchangePartForMachine;
use Inventory\Domain\Transaction\GI\GIforMachineNoExchange;
use Inventory\Domain\Transaction\GI\GIforReturnPO;
use Inventory\Domain\Transaction\GI\GIforTransferLocation;
use Inventory\Domain\Transaction\GI\GIforTransferWarehouse;
use Inventory\Domain\Transaction\GR\GRForAdjustment;
use Inventory\Domain\Transaction\GR\GRFromExchange;
use Inventory\Domain\Transaction\GR\GRFromGIReversal;
use Inventory\Domain\Transaction\GR\GRFromOpening;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;
use Inventory\Domain\Transaction\GR\GRFromTransferLocation;
use Inventory\Domain\Transaction\GR\GRFromTransferWarehouse;
use Inventory\Domain\Transaction\GR\GRWithoutInvoice;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Procure\Domain\GoodsReceipt\GenericGR;
use Webmozart\Assert\Assert;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TransactionFactory
{

    /**
     *
     * @param GenericTrx $sourceObj
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Inventory\Domain\Transaction\GR\GRFromExchange
     */
    public static function createAndPostReversal(GenericTrx $sourceObj, TrxSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        if ($sourceObj->getMovementFlow() == TrxFlow::WH_TRANSACTION_IN) {
            return GIFromGRReversal::postCopyFromTrxReversal($sourceObj, $options, $sharedService);
        }

        if ($sourceObj->getMovementFlow() == TrxFlow::WH_TRANSACTION_OUT) {
            return GRFromGIReversal::postCopyFromTrxReversal($sourceObj, $options, $sharedService);
        }
    }

    /**
     *
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public static function createFrom(TrxSnapshot $snapshot, CommandOptions $options, SharedService $sharedService)
    {
        Assert::notNull($snapshot, "AP snapshot not found");
        Assert::notNull($options, "Command options not found");
        Assert::notNull($sharedService, "Trx shared service options not found");

        $typeId = $snapshot->getMovementType();
        $instance = self::createTrx($typeId);

        $snapshot->initDoc($options);
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        TrxSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);

        $instance->specify(); // Important

        $validatorService = ValidatorFactory::create($typeId, $sharedService);
        $instance->validateHeader($validatorService->getHeaderValidators()); // validate header only.

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxCmdRepositoryInterface $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new TrxHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        $instance->updateIdentityFrom($rootSnapshot);

        return $instance;
    }

    /**
     *
     * @param GenericTrx $rootEntity
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @param array $params
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return NULL|\Inventory\Domain\Transaction\GI\GIFromPurchasingReversal
     */
    public static function updateFrom(GenericTrx $rootEntity, TrxSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $params)
    {
        Assert::notNull($rootEntity, "Trx root entity not found");
        Assert::notNull($snapshot, "Trx new snapshot not found");
        Assert::notNull($options, "Command options not found");
        Assert::notNull($sharedService, "Trx shared service options not found");

        $typeId = $snapshot->getMovementType();
        $instance = self::createTrx($typeId);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $snapshot->setExchangeRate($fxRate);

        // SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        TrxSnapshotAssembler::updateEntityExcludedDefaultFieldsFrom($rootEntity, $snapshot);

        $validatorService = ValidatorFactory::create($typeId, $sharedService);

        // validate header only.
        $instance->validateHeader($validatorService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getNotification()->errorMessage());
        }

        $instance->clearEvents();

        /**
         *
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxCmdRepositoryImpl $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance);

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new TrxHeaderUpdated($target, $defaultParams, $params);
        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param TrxSnapshot $snapshot
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
    public static function contructFromDB(TrxSnapshot $snapshot)
    {
        Assert::notNull($snapshot, "Trx snapshot not found");

        $typeId = $snapshot->getMovementType();
        $instance = self::createTrx($typeId);

        /**
         *
         * @var GenericTrx $trx
         */
        TrxSnapshotAssembler::updateEntityAllFieldsFrom($instance, $snapshot);
        Assert::notNull($instance, "Trx is null");

        // Important
        $instance->specify();
        $instance->updateStatus();

        return $instance;
    }

    /**
     *
     * @param GenericGR $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     */
    public static function postCopyFromProcureGR(GenericGR $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRFromPurchasing::postCopyFromProcureGR($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericGR $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Inventory\Domain\Transaction\GI\GIFromPurchasingReversal
     */
    public static function postCopyFromProcureGRReversal(GenericGR $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GIFromPurchasingReversal::postCopyFromProcureGRReversal($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Inventory\Domain\Transaction\GR\GRFromExchange
     */
    public static function postCopyFromGI(GenericTrx $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRFromExchange::postCopyFromGI($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @return \Inventory\Domain\Transaction\GR\GRFromTransferWarehouse
     */
    public static function postCopyFromTO(GenericTrx $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        return GRFromTransferWarehouse::postCopyFromTO($sourceObj, $options, $sharedService);
    }

    /**
     *
     * @param string $typeId
     */
    private static function createTrx($typeId)
    {
        if (! \in_array($typeId, TrxType::getSupportedTransaction())) {
            throw new InvalidArgumentException("Movemement type empty or not supported! " . $typeId);
        }

        $trx = null;
        switch ($typeId) {

            // ============
            case TrxType::GR_FROM_OPENNING_BALANCE:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_PURCHASING:
                $trx = new GRFromPurchasing(); // auto
                break;

            case TrxType::GR_FROM_EXCHANGE:
                $trx = new GRFromExchange(); // auto
                break;

            case TrxType::GR_WITHOUT_INVOICE:
                $trx = new GRWithoutInvoice(); // manual
                break;

            case TrxType::GR_FROM_TRANSFER_LOCATION:
                $trx = new GRFromTransferLocation();
                break;

            case TrxType::GR_FROM_TRANSFER_WAREHOUSE:
                $trx = new GRFromTransferWarehouse(); // auto
                break;

            case TrxType::GR_FOR_ADJUSTMENT_AFTER_COUNTING:
                $trx = new GRForAdjustment();
                break;

            // ============
            case TrxType::GI_FOR_COST_CENTER:
                $trx = new GIforCostCenter();
                break;
            case TrxType::GI_FOR_REPAIR_MACHINE_WITH_EX:
                $trx = new GIforExchangePartForMachine();
                break;
            case TrxType::GI_FOR_REPAIR_MACHINE:
                $trx = new GIforMachineNoExchange();
                break;

            case TrxType::GI_FOR_TRANSFER_LOCATION:
                $trx = new GIforTransferLocation();
                break;

            case TrxType::GI_FOR_TRANSFER_WAREHOUSE:
                $trx = new GIforTransferWarehouse();
                break;

            case TrxType::GI_FOR_RETURN_PO:
                $trx = new GIforReturnPO();
                break;
            case TrxType::GI_FOR_ADJUSTMENT_AFTER_COUNTING:
                $trx = new GIforAdjustment();
                break;

            case TrxType::GI_FOR_PURCHASING_REVERSAL:
                $trx = new GIFromPurchasingReversal(); // auto
                break;
        }

        Assert::notNull($trx, \sprintf("Can not create transaction #%s. Might not be supported yet", $typeId));

        return $trx;
    }
}