<?php
namespace Inventory\Domain\Transaction\Factory;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\TrxHeaderCreated;
use Inventory\Domain\Event\Transaction\TrxHeaderUpdated;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Transaction\GI\GIforExchangePartForMachine;
use Inventory\Domain\Transaction\GI\GIforMachineNoExchange;
use Inventory\Domain\Transaction\GI\GIforTransferLocation;
use Inventory\Domain\Transaction\GI\GIforTransferWarehouse;
use Inventory\Domain\Transaction\GR\GRFromOpening;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;
use Inventory\Domain\Transaction\GR\GRFromTransferLocation;
use Inventory\Domain\Transaction\GR\GRFromTransferWarehouse;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
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
        if (! $snapshot instanceof TrxSnapshot) {
            throw new InvalidArgumentException("TrxSnapshot not found!");
        }

        $typeId = $snapshot->getMovementType();

        if (! \in_array($typeId, TrxType::getSupportedTransaction())) {
            throw new InvalidArgumentException("Movemement type empty or not supported! TransactionFactory #" . $snapshot->getMovementType());
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        $trx = null;

        switch ($typeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_PURCHASING:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_TRANSFER_LOCATION:
                $trx = new GRFromTransferLocation();
                break;

            case TrxType::GR_FROM_TRANSFER_WAREHOUSE:
                $trx = new GRFromTransferWarehouse();
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
        }

        if ($trx == null) {
            throw new \RuntimeException(\sprintf("Can not create transaction #%s. Might not be supported yet", $typeId));
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->init($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericTrx $trx
         */
        SnapshotAssembler::makeFromSnapshot($trx, $snapshot);

        // Important
        $trx->specify();

        $validatorService = ValidatorFactory::create($typeId, $sharedService);

        // validate header only.
        $trx->validateHeader($validatorService->getHeaderValidators());

        if ($trx->hasErrors()) {
            throw new \RuntimeException($trx->getNotification()->errorMessage());
        }

        $trx->recordedEvents = array();

        /**
         *
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxCmdRepositoryImpl $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($trx);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when creating trx #%s", $trx->getId()));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new TrxHeaderCreated($target, $defaultParams, $params);
        $trx->addEvent($event);
        return $trx;
    }

    public static function updateFrom(TrxSnapshot $snapshot, CommandOptions $options, SharedService $sharedService, $params)
    {
        if (! $snapshot instanceof TrxSnapshot) {
            throw new InvalidArgumentException("TrxSnapshot not found!");
        }

        $typeId = $snapshot->getMovementType();

        if (! \in_array($typeId, TrxType::getSupportedTransaction())) {
            throw new InvalidArgumentException("Movemement type empty or not supported! " . $snapshot->getMovementType());
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        $trx = null;

        switch ($typeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_PURCHASING:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_TRANSFER_LOCATION:
                $trx = new GRFromTransferLocation();
                break;

            case TrxType::GR_FROM_TRANSFER_WAREHOUSE:
                $trx = new GRFromTransferWarehouse();
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
        }

        if ($trx == null) {
            throw new \RuntimeException(\sprintf("Can not create transaction #%s. Might not be supported yet", $typeId));
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->markAsChange($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        /**
         *
         * @var GenericTrx $trx
         */
        SnapshotAssembler::makeFromSnapshot($trx, $snapshot);

        // Important
        $trx->specify();

        $validatorService = ValidatorFactory::create($typeId, $sharedService);

        // validate header only.
        $trx->validateHeader($validatorService->getHeaderValidators());

        if ($trx->hasErrors()) {
            throw new \RuntimeException($trx->getNotification()->errorMessage());
        }

        $trx->recordedEvents = array();

        /**
         *
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxCmdRepositoryImpl $rep ;
         */

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($trx);

        if ($rootSnapshot == null) {
            throw new RuntimeException(sprintf("Error orcured when updating trx #%s", $trx->getId()));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new TrxHeaderUpdated($target, $defaultParams, $params);
        $trx->addEvent($event);
        return $trx;
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
        if (! $snapshot instanceof TrxSnapshot) {
            throw new InvalidArgumentException("TrxSnapshot not found!");
        }

        $typeId = $snapshot->getMovementType();

        if (! \in_array($typeId, TrxType::getSupportedTransaction())) {
            throw new InvalidArgumentException("Movemement type empty or not supported! TransactionFactory #" . $snapshot->getMovementType());
        }

        $trx = null;

        switch ($typeId) {

            case TrxType::GR_FROM_OPENNING_BALANCE:
                $trx = new GRFromOpening();
                break;

            case TrxType::GR_FROM_TRANSFER_LOCATION:
                $trx = new GRFromTransferLocation();
                break;

            case TrxType::GR_FROM_PURCHASING:
                $trx = new GRFromPurchasing();
                break;

            case TrxType::GI_FOR_COST_CENTER:
                $trx = new GIforCostCenter();
                break;
            case TrxType::GI_FOR_REPAIR_MACHINE_WITH_EX:
                $trx = new GIforExchangePartForMachine();
                break;
            case TrxType::GI_FOR_REPAIR_MACHINE:
                $trx = new GIforMachineNoExchange();
                break;
        }

        if ($trx == null) {
            throw new \RuntimeException(\sprintf("Can not create transaction #%s. Might not be supported yet", $typeId));
        }

        /**
         *
         * @var GenericTrx $trx
         */
        SnapshotAssembler::makeFromSnapshot($trx, $snapshot);

        // Important
        $trx->specify();
        return $trx;
    }
}