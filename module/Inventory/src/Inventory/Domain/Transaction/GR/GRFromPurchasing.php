<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Event\Transaction\GR\WhGrReversed;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Exception\ValidationFailedException;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\Contracts\PostingServiceInterface;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\GoodsReceipt;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\Contracts\GoodsReceiptInterface;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Inventory\Domain\Warehouse\Transaction\TransactionFlow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasing extends GoodsReceipt implements GoodsReceiptInterface
{

    public function __construct()
    {
        $this->movementType = TransactionType::GR_FROM_PURCHASING;
        $this->movementFlow = TransactionFlow::WH_TRANSACTION_IN;
    }

    public static function postCopyFromProcureGR(GRDoc $sourceObj, CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof GRDoc) {
            throw new InvalidArgumentException("GRDoc Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("GRDoc Entity is empty!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("GR document is not posted yet!");
        }

        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        /**
         *
         * @var \Inventory\Domain\Transaction\GR\GRFromPurchasing $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        // $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());
        $instance->setRemarks($instance->getRemarks() . \sprintf(' /PO-GR %s', $sourceObj->getId()));

        foreach ($rows as $r) {

            /**
             *
             * @var GRRow $r ;
             */

            if (! $r->getIsInventoryItem()) {
                continue;
            }

            $grRow = GRFromPurchasingRow::createFromPurchaseGrRow($instance, $r, $options);
            $grRow->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }

        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof TrxSnapshot) {
            throw new OperationFailedException(sprintf("Error orcured when creating WH-GR #%s", $instance->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());

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

    /**
     *
     * @param GRDoc $sourceObj
     * @param CommandOptions $options
     * @param TrxValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasing
     */
    public static function postCopyFromProcureGrReversal(GRDoc $sourceObj, CommandOptions $options, TrxValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof GRDoc) {
            throw new InvalidArgumentException("GRDoc Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("GRDoc Entity is empty!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_REVERSED) {
            throw new InvalidArgumentException("GR document is not reversed yet!");
        }

        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not given!");
        }

        if (! $validationService->getHeaderValidators() instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        if (! $validationService->getRowValidators() instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Headers Validators not given!");
        }

        /**
         *
         * @var \Inventory\Domain\Transaction\GR\GRFromPurchasing $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        // $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.
        $instance->markAsReversed($createdBy, $sourceObj->getPostingDate());
        $instance->setRemarks($instance->getRemarks() . \sprintf(' /PO-GR %s', $sourceObj->getId()));
        foreach ($rows as $r) {

            /**
             *
             * @var GRRow $r ;
             */

            $grRow = GRFromPurchasingRow::createFromPurchaseGrRowReversal($instance, $r, $options);
            $grRow->markAsReversed($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }

        $instance->validate($validationService);

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof TrxSnapshot) {
            throw new OperationFailedException(sprintf("Error orcured when creating WH-GR #%s", $instance->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());

        $target = $instance;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new WhGrReversed($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param GRDoc $sourceObj
     * @param TrxSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param PostingServiceInterface $postingService
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasing
     */
    public function createFromProcureGR(GRDoc $sourceObj, TrxSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {
        if (! $sourceObj instanceof GRDoc) {
            throw new InvalidArgumentException("GR Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("Source Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("Source document is not posted!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        $sourceObj->convertTo($this);

        // overwrite.
        // $this->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $this->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $this->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var TrxRow $r ;
             */

            // ignore none-inventory item;
            if (! $r->getIsInventoryItem()) {
                continue;
            }

            $localEntity = GRFromPurchasingRow::createFromPurchaseGrRow($this, $r, $options);
            $this->addRow($localEntity);
            $this->validateRow($localEntity, $rowValidators);
        }
        return $this;
    }
}