<?php
namespace Inventory\Domain\Transaction\GI;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GI\WhGiPosted;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\AbstractGoodsIssue;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\GoodsIssueInterface;
use Inventory\Domain\Transaction\Contracts\StockQtyAdjustmentInterface;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\Contracts\TrxStatus;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Validator\ValidatorFactory;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;
use Inventory\Domain\Event\Transaction\GI\WhGiForReturnPoPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforReturnPO extends AbstractGoodsIssue implements GoodsIssueInterface, StockQtyAdjustmentInterface
{

    protected function afterPost(CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        $target = $this->makeDetailsDTO();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhGiForReturnPoPosted($target, $defaultParams, $params);
        $this->addEvent($event);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\GenericTrx::specify()
     */
    public function specify()
    {
        $this->movementType = TrxType::GI_FOR_RETURN_PO;
        $this->movementFlow = TrxFlow::WH_TRANSACTION_OUT;
    }

    /**
     *
     * @param GenericTrx $sourceObj
     * @param CommandOptions $options
     * @param TrxValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Inventory\Domain\Transaction\GI\GIforReturnPO
     */
    public static function createFromGRFromPurchasing(GenericTrx $sourceObj, CommandOptions $options, SharedService $sharedService)
    {
        if (! $sourceObj instanceof GenericTrx) {
            throw new InvalidArgumentException("Trx Entity is required");
        }

        if ($sourceObj->getMovementType() != TrxType::GR_FROM_PURCHASING) {
            throw new InvalidArgumentException("Return PO not possible! It is only posible for goods receipt from purchasing!");
        }

        if ($sourceObj->getTransactionStatus() == TrxStatus::GR_FULLY_USED) {
            throw new InvalidArgumentException("Items already used fullly. Return not possible!");
        }
        if ($sourceObj->getDocStatus() !== ProcureDocStatus::POSTED) {
            throw new InvalidArgumentException("GR document is not posted yet!");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("TrxDoc Entity is empty!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Inventory\Domain\Transaction\GI\GIforReturnPO $instance ;
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // Important
        $instance->setMovementFlow(TrxFlow::WH_TRANSACTION_OUT);
        $instance->setRelevantMovementId($sourceObj->getId());
        $instance->setRemarks($instance->getRemarks() . \sprintf('%s', '[Return]'));

        foreach ($rows as $r) {

            /**
             *
             * @var TrxRow $r ;
             */
            $grRow = GIforReturnPORow::createFromGRFromPurchasingRow($instance, $r, $options);
            $grRow->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }

        $instance->specify();
        $validationService = ValidatorFactory::create($instance->getMovementType(), $sharedService);

        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        $instance->clearEvents();
        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->store($instance);

        if (! $snapshot instanceof TrxSnapshot) {
            throw new \RuntimeException(sprintf("Error orcured when creating WH-GI for return PO #%s", $instance->getId()));
        }

        $instance->updateIdentityFrom($snapshot);
        return $instance;
    }
}