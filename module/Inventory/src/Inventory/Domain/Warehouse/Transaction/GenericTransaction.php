<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Service\TransactionValuationService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericTransaction extends AbstractTransaction
{

    /**
     *
     * @var array
     */
    protected $transactionRows;

    /**
     *
     * @var array
     */
    protected $recordedEvents;

    /**
     *
     * @var int
     */
    public $totalActiveRows;

    /**
     *
     * @var string
     */
    public $transtionRowsOutput;

    abstract protected function prePost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null);

    abstract protected function doPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null);

    abstract protected function afterPost(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null);

    abstract protected function raiseEvent();

    abstract protected function specificValidation(TransactionSpecificationService $specificationService, Notification $notification = null);

    abstract protected function specificHeaderValidation(TransactionSpecificationService $specificationService, Notification $notification = null);

    abstract protected function specificRowValidation(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false);

    abstract protected function specificRowValidationByFlow(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification = null, $isPosting = false);

    abstract public function addTransactionRow(TransactionRow $transactionRow);

    /**
     *
     * @param TransactionPostingService $postingService
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @return Notification
     */
    public function post(TransactionSpecificationService $specificationService, TransactionValuationService $valuationService, TransactionPostingService $postingService, Notification $notification = null)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($valuationService == null) {
            throw new InvalidArgumentException("Valuation service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        if ($notification == null) {
            $notification = new Notification();
        }

        $this->recordedEvents = array();

        $prePostNotify = $this->prePost($specificationService, $valuationService, $postingService, $notification);
        if (! $prePostNotify == null)
            $notification = $prePostNotify;

        $doPostNotify = $this->doPost($specificationService, $valuationService, $postingService, $notification);

        if (! $doPostNotify == null)
            $notification = $doPostNotify;

        $afterPostNotify = $this->afterPost($specificationService, $valuationService, $postingService, $notification);

        if (! $afterPostNotify == null)
            $notification = $afterPostNotify;

        if (! $notification->hasErrors()) {
            $this->raiseEvent();
        }

        return $notification;
    }

    /**
     *
     * @param TransactionRow $transactionRow
     */
    public function addRow(TransactionRow $transactionRow)
    {
        $this->transactionRows[] = $transactionRow;
    }

    /**
     *
     * @param TransactionRow $transactionRow
     */
    public function addRowFromSnapshot(TransactionSpecificationService $specificationService, TransactionRowSnapshot $snapshot, Notification $notification)
    {
        if (! $snapshot instanceof TransactionRowSnapshot)
            return null;

        $snapshot->mvUuid = $this->uuid;
        $snapshot->docType = $this->movementType;
        $snapshot->transactionType = $this->movementType;
        $snapshot->flow = $this->movementFlow;
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->wh = $this->warehouse;
        $snapshot->trxDate = $this->movementDate;

        $row = new TransactionRow();
        $row->makeFromSnapshot($snapshot);

        $ckResult = $this->validateRow($specificationService, $row, $notification, false);
        if ($ckResult->hasErrors())
            throw new InvalidArgumentException($ckResult->errorMessage());

        $this->transactionRows[] = $row;

        return $row;
    }

    /**
     *
     * @return mixed
     */
    public function getRows()
    {
        return $this->transactionRows;
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {

        /**
         *
         * @var Notification $notification
         */
        $notification = $this->validate();

        if ($notification == null)
            return false;

        return ! $notification->hasErrors();
    }

    /**
     * validation all
     *
     * @return Notification
     */
    public function validate(TransactionSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($notification == null)
            $notification = new Notification();

        $notification = $this->validateHeader($specificationService, $notification, $isPosting);

        if ($notification->hasErrors())
            return $notification;

        $specificValidationResult = $this->specificValidation($specificationService, $notification, $isPosting);
        if ($specificValidationResult instanceof Notification)
            $notification = $specificValidationResult;

        if ($notification->hasErrors())
            return $notification;

        if (count($this->transactionRows) == 0) {
            $notification->addError("Transaction has no lines");
            return $notification;
        }

        foreach ($this->transactionRows as $row) {

            $checkRowResult = $this->validateRow($specificationService, $row, $notification, $isPosting);

            if ($checkRowResult !== null)
                $notification = $checkRowResult;
        }

        return $notification;
    }

    /**
     * validation header
     *
     * @return Notification
     */
    public function validateHeader(TransactionSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($specificationService == null) {
            $notification->addError("Specification service not found");
            return $notification;
        }

        // always done
        $notification = $this->generalHeaderValidation($specificationService, $notification);

        if ($notification->hasErrors())
            return $notification;

        // specific
        $specificHeaderValidationResult = $this->specificHeaderValidation($specificationService, $notification);

        if ($specificHeaderValidationResult instanceof Notification)
            $notification = $specificHeaderValidationResult;

        return $notification;
    }

    /**
     *
     * @param TransactionSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalHeaderValidation(TransactionSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($specificationService == null) {
            $notification->addError("Specification not found");
            return $notification;
        }

        /**
         *
         * @var TransactionSpecificationService $specificationService ;
         */

        $notification = $specificationService->doGeneralHeaderValiation($this, $notification);

        return $notification;
    }

    /**
     * validation row
     *
     * @return Notification
     */
    public function validateRow(TransactionSpecificationService $specificationService, TransactionRow $row, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($specificationService == null) {
            $notification->addError("Specification not found");
            return $notification;
        }

        // allways done
        $notification = $this->generalRowValidation($specificationService, $row, $notification, $isPosting);

        if ($notification->hasErrors())
            return $notification;

        // validation by flow
        $specificRowValidationByFlowResult = $this->specificRowValidationByFlow($specificationService, $row, $notification, $isPosting);

        // specifict validation
        if ($specificRowValidationByFlowResult != null) {
            $notification = $specificRowValidationByFlowResult;
        }

        $specificRowValidationResult = $this->specificRowValidation($specificationService, $row, $notification, $isPosting);
        if ($specificRowValidationResult != null)
            $notification = $specificRowValidationResult;

        return $notification;
    }

    /**
     * Default - can be overwritten.
     *
     * @param TransactionRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     * @return string|\Application\Notification
     */
    protected function generalRowValidation($specificationService, TransactionRow $row, $notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($row == null) {
            $notification->addError("No row found");
        }

        if ($specificationService == null) {
            $notification->addError("Specification service not found");
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        /**
         *
         * @var TransactionSpecificationService $specificationService ;
         */
        $notification = $specificationService->doGeneralRowValiation($this, $row, $notification);

        return $notification;
    }

    /**
     *
     * @return array
     */
    public function getTransactionRows()
    {
        return $this->transactionRows;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalActiveRows()
    {
        return $this->totalActiveRows;
    }

    /**
     *
     * @param mixed $totalActiveRows
     */
    public function setTotalActiveRows($totalActiveRows)
    {
        $this->totalActiveRows = $totalActiveRows;
    }

    /**
     *
     * @return mixed
     */
    public function getTranstionRowsOutput()
    {
        return $this->transtionRowsOutput;
    }

    /**
     *
     * @param mixed $transtionRowsOutput
     */
    public function setTranstionRowsOutput($transtionRowsOutput)
    {
        $this->transtionRowsOutput = $transtionRowsOutput;
    }

    public function getRecordedEvents()
    {
        return $this->recoredEvents;
    }
}