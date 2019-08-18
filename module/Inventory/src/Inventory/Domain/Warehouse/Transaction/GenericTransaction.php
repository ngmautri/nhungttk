<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Inventory\Application\Service\Item\FIFOService;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\ValuationServiceInterface;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Service\FIFOLayerServiceFactory;
use Inventory\Domain\Service\FIFOServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericTransaction extends AbstractTransaction
{

    /**
     *
     * @var WarehouseQueryRepositoryInterface $warehouseQueryRepository;
     */
    protected $warehouseQueryRepository;

    /**
     *
     * @var array
     */
    protected $transactionRows;

    /**
     *
     * @var ValuationServiceInterface
     */
    protected $valuationService;

    /**
     *
     * @var FIFOServiceInterface $fifoService;
     */
    protected $fifoService;

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

    abstract protected function prePost(TransactionPostingService $postingService = null, $notification = null);

    abstract protected function doPost(TransactionPostingService $postingService = null, $notification = null);

    abstract protected function afterPost(TransactionPostingService $postingService = null, $notification = null);

    abstract protected function raiseEvent();

    abstract protected function specificValidation($notification = null);

    abstract protected function specificHeaderValidation($notification = null);

    abstract protected function specificRowValidation($row, $notification = null, $isPosting = false);

    abstract protected function specificRowValidationByFlow($row, $notification = null, $isPosting = false);

    abstract public function addTransactionRow($transactionRow);

    /**
     *
     * @param TransactionPostingService $postingService
     *            ;
     * @param Notification $notification
     *            ;
     * @throws InvalidArgumentException ;
     * @return Notification ;
     */
    public function post(TransactionPostingService $postingService = null, Notification $notification = null)
    {
        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        if ($notification == null) {
            $notification = new Notification();
        }

        $this->recordedEvents = array();

        $notification = $this->prePost($postingService, $notification);
        $notification = $this->doPost($postingService, $notification);
        $notification = $this->afterPost($postingService);
        
        if(!$notification->hasErrors()){
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
    public function addRowFromSnapshot(TransactionRowSnapshot $snapshot)
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

        $ckResult = $this->validateRow($row, null, false);
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
    public function validate($notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if (! $this->cmdRepository instanceof TransactionCmdRepositoryInterface)
            $notification->addError("Cmd repository is set");

        if (! $this->queryRepository instanceof TransactionQueryRepositoryInterface)
            $notification->addError("Query repository is set");

        $notification = $this->validateHeader($notification);

        if ($notification->hasErrors())
            return $notification;

        $specificValidationResult = $this->specificValidation($notification);
        if ($specificValidationResult instanceof Notification)
            $notification = $specificValidationResult;

        if ($notification->hasErrors())
            return $notification;

        if (count($this->transactionRows) == 0) {
            $notification->addError("Transaction has no lines");
            return $notification;
        }

        foreach ($this->transactionRows as $row) {

            $checkRowResult = $this->validateRow($row, $notification);

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
    public function validateHeader($notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        // always done
        $notification = $this->generalHeaderValidation($notification);

        if ($notification->hasErrors())
            return $notification;

        // specific
        $specificHeaderValidationResult = $this->specificHeaderValidation($notification);
        if ($specificHeaderValidationResult instanceof Notification)
            $notification = $specificHeaderValidationResult;

        if ($notification->hasErrors())
            return $notification;

        return $notification;
    }

    /**
     *
     * @param Notification $notification
     * @return string|\Application\Notification
     */
    protected function generalHeaderValidation($notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->domainSpecificationFactory == null || $this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        if ($notification->hasErrors())
            return $notification;

        // do verification now

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits. #" . $this->company);
        }

        // check movement type
        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->movementType)) {
            $notification->addError("Transaction Type is not set.");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($this->movementType, $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }

            // check if transactition type is consistent - when change header

            if (count($this->transactionRows) > 0) {
                foreach ($this->transactionRows as $row) {
                    /**
                     *
                     * @var TransactionRow $row ;
                     */
                    if ($row->getTransactionType() !== $this->movementType) {
                        $notification->addError("Transaction Type is inconsistent, because at least one row has different type");
                        break;
                    }
                }
            }
        }

        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($this->movementDate)) {
            $notification->addError("Transaction date is not correct or empty");
        } else {

            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $this->company,
                "movementDate" => $this->movementDate
            );

            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post on this date. Period is not created or closed." . $this->movementDate);
            }
        }

        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($this->docCurrency))
            $notification->addError("Currency is empty or invalid");

        // check warehouse currency
        if ($this->warehouse == null) {
            $notification->addError("Source warehouse is not set");
        } else {

            $spec1 = $this->sharedSpecificationFactory->getWarehouseACLSpecification();
            $subject = array(
                "companyId" => $this->company,
                "warehouseId" => $this->warehouse,
                "userId" => $this->createdBy
            );
            if (! $spec1->isSatisfiedBy($subject))
                $notification->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $this->company, $this->warehouse, $this->createdBy));
        }

        // check local currency
        if ($this->localCurrency == null) {
            $notification->addError("Local currency is not set");
        } else {
            $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($this->localCurrency))
                $notification->addError("Local currency not exits..." . $this->localCurrency);
        }

        return $notification;
    }

    /**
     * validation row
     *
     * @return Notification
     */
    public function validateRow($row, $notification, $isPosting = false)
    {
        
        if ($this->sharedSpecificationFactory == null) {
            $notification->addError("Validators is not found");
            return $notification;
        }
        
        
        if ($notification == null)
            $notification = new Notification();

        // allways done
        $notification = $this->generalRowValidation($row, $notification, $isPosting);

        if ($notification->hasErrors())
            return $notification;

        // validation by flow    
        $specificRowValidationByFlowResult = $this->specificRowValidationByFlow($row, $notification, $isPosting);

        // specifict validation
        if ($specificRowValidationByFlowResult != null) {
            $notification = $specificRowValidationByFlowResult;
        }

        $specificRowValidationResult = $this->specificRowValidation($row, $notification, $isPosting);
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
    protected function generalRowValidation(TransactionRow $row, $notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($row == null)
            return $notification;

        /**
         *
         * @var AbstractSpecificationForCompany $spec ;
         */
        
        if ($row->getMvUuid() !== $this->uuid) {
            $notification->addError("transaction id not match");
            return $notification;
        }

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();
        $spec->setCompanyId($this->company);

        $subject = array(
            "companyId" => $this->company,
            "itemId" => $row->getItem()
        );

        if (! $spec->isSatisfiedBy($subject))
            $notification->addError("Item not exits in the company #" . $this->company);

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($row->getDocQuantity()))
            $notification->addError("Quantity is not valid! " . $row->getDocQuantity());

        return $notification;
    }

    /**
     *
     * @return \Inventory\Domain\Service\ValuationServiceInterface
     */
    public function getValuationService()
    {
        return $this->valuationService;
    }

    /**
     *
     * @param \Inventory\Domain\Service\ValuationServiceInterface $valuationService
     */
    public function setValuationService($valuationService)
    {
        $this->valuationService = $valuationService;
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

    /**
     *
     * @return \Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface
     */
    public function getWarehouseQueryRepository()
    {
        return $this->warehouseQueryRepository;
    }

    /**
     *
     * @param WarehouseQueryRepositoryInterface $warehouseQueryRepository
     */
    public function setWarehouseQueryRepository(WarehouseQueryRepositoryInterface $warehouseQueryRepository)
    {
        $this->warehouseQueryRepository = $warehouseQueryRepository;
    }

    public function getRecordedEvents()
    {
        return $this->recoredEvents;
    }
    
   /**
    * 
    * @return \Inventory\Domain\Service\FIFOServiceInterface
    */
    public function getFifoService()
    {
        return $this->fifoService;
    }

    /**
     * 
     * @param FIFOServiceInterface $fifoService
     */
    public function setFifoService(FIFOServiceInterface $fifoService)
    {
        $this->fifoService = $fifoService;
    }



    
}