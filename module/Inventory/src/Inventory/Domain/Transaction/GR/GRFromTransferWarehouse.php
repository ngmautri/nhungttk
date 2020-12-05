<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
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
use Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromTransferWarehouse extends AbstractGoodsReceipt implements GoodsReceiptInterface, BackGroundTrxInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GR_FROM_TRANSFER_WAREHOUSE;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_IN;
    }

    /**
     * TO: Transer Order;
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws RuntimeException
     * @return \Inventory\Domain\Transaction\GR\GRFromTransferWarehouse
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

        if ($sourceObj->getMovementType() != TrxType::GI_FOR_TRANSFER_WAREHOUSE) {
            throw new InvalidArgumentException("WH-TO is required!");
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

        $sourceWH = $whQueryRep->getById($sourceObj->getWarehouse());
        if (! $sourceWH instanceof GenericWarehouse) {
            throw new InvalidArgumentException("WH not found");
        }

        $targeWH = $whQueryRep->getById($sourceObj->getTargetWarehouse());

        if (! $targeWH instanceof GenericWarehouse) {
            throw new InvalidArgumentException("WH not found");
        }

        if (! $sourceWH instanceof GenericWarehouse) {
            throw new InvalidArgumentException("WH not found");
        }

        /**
         *
         * @var \Inventory\Domain\Transaction\GR\GRFromTransferWarehouse $instance
         */

        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->specify(); // important
        $validationService = ValidatorFactory::create($instance->getMovementType(), $sharedService);

        // Important: Source Warehouse
        $instance->setWarehouse($targeWH->getId());

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($options);

        // overwrite.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto] WH-TO from ', $sourceObj->getSysNumber()));

        foreach ($rows as $r) {

            /**
             *
             * @var TrxRow $r ;
             */

            $grRow = GRFromTransferWarehouseRow::createFromTORow($instance, $r, $options);
            $grRow->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
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
            throw new RuntimeException(sprintf("Error orcured when creating WH-TO #%s", $instance->getId()));
        }

        $instance->updateIdentityFrom($snapshot);

        $target = $instance;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhGrPosted($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }
}