<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\AbstractGoodsReceipt;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\BackGroundTrxInterface;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromTransferLocation extends AbstractGoodsReceipt implements GoodsReceiptInterface, BackGroundTrxInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_TRANSFER_LOCATION;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }

    /**
     * TO: Transfer Order.
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Transaction\GR\GRFromExchange
     */
    public static function postCopyFromTO(GenericTrx $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx is required . " . \get_class($sourceObj));
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("GenericTrx Entity is empty!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() != ProcureDocStatus::POSTED) {
            throw new InvalidArgumentException("GR document is not posted yet!");
        }

        $whQueryRep = $sharedService->getWhQueryRepository();

        if (! $whQueryRep instanceof WhQueryRepositoryInterface) {
            throw new InvalidArgumentException("WH Query is required!");
        }

        $wh = $whQueryRep->getById($sourceObj->getWarehouse());

        if (! $wh instanceof GenericWarehouse) {
            throw new InvalidArgumentException("WH not found");
        }

        $recycleLocation = $wh->getRecycleLocation();

        if (! $recycleLocation instanceof BaseLocation) {
            throw new InvalidArgumentException("WH Recycle bin not found");
        }

        /**
         *
         * @var \Inventory\Domain\Transaction\GR\GRFromTransferLocation $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // Important: Update Recycle Location:
        $instance->setTartgetLocation($recycleLocation->getId());

        $instance->specify();
        $validationService = ValidatorFactory::create($instance->getMovementType(), $sharedService);

        $createdBy = $options->getUserId();
        $instance->initDoc($options);

        // overwrite.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());
        $instance->setRemarks($instance->getRemarks() . \sprintf('Auto./WH-GE %s', $sourceObj->getId()));

        foreach ($rows as $r) {

            /**
             *
             * @var TrxRow $r ;
             */

            $grRow = GRFromTransferWarehouseRow::createFromTORow($instance, $r, $options);
            $grRow->markRowAsPosted($instance, $options);
            $instance->addRow($grRow);
        }

        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        // clear all recorded event, if any.
        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof TrxSnapshot) {
            throw new RuntimeException(sprintf("Error orcured when creating GE-Exchange #%s", $instance->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());
        return $instance;
    }
}