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
use Inventory\Domain\Transaction\GI\GIforAdjustment;
use Inventory\Domain\Transaction\GI\GIforCostCenter;
use Inventory\Domain\Transaction\GI\GIforExchangePartForMachine;
use Inventory\Domain\Transaction\GI\GIforMachineNoExchange;
use Inventory\Domain\Transaction\GI\GIforTransferLocation;
use Inventory\Domain\Transaction\GI\GIforTransferWarehouse;
use Inventory\Domain\Transaction\GR\GRForAdjustment;
use Inventory\Domain\Transaction\GR\GRFromExchange;
use Inventory\Domain\Transaction\GR\GRFromOpening;
use Inventory\Domain\Transaction\GR\GRFromTransferLocation;
use Inventory\Domain\Transaction\GR\GRFromTransferWarehouse;
use Inventory\Domain\Transaction\GR\GRWithoutInvoice;
use Inventory\Domain\Transaction\Repository\TrxCmdRepositoryInterface;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use InvalidArgumentException;
use RuntimeException;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;

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

        $trx = self::createTrx($typeId);

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

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $trx->updateExchangeRate($fxRate);

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
         * @var TrxCmdRepositoryInterface $rep ;
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

        $trx->updateIdentityFrom($rootSnapshot);

        return $trx;
    }

    /**
     *
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @param array $params
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Transaction\GenericTrx
     */
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

        $trx = self::createTrx($typeId);

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
        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $trx->updateExchangeRate($fxRate);

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

        $trx = self::createTrx($typeId);

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

    /**
     *
     * @param string $typeId
     */
    private static function createTrx($typeId)
    {
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
                $trx = new GIforTransferWarehouse();
                break;
            case TrxType::GI_FOR_ADJUSTMENT_AFTER_COUNTING:
                $trx = new GIforAdjustment();
                break;
        }

        return $trx;
    }
}