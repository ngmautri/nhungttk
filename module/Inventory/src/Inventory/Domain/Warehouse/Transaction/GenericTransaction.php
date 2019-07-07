<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\ValuationServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericTransaction extends AbstractTransaction
{

    /**
     *
     * @var AbstractSpecificationFactory
     */
    protected $sharedSpecificationFactory;

    /**
     *
     * @var \Inventory\Domain\AbstractSpecificationFactory
     */
    protected $domainSpecificationFactory;

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

    public $totalActiveRows;

    public $transtionRowsOutput;

    /**
     *
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
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

    abstract public function prePost();

    abstract public function afterPost();

    abstract public function post();

    abstract public function specificValidation($notification = null);

    abstract public function specificHeaderValidation($notification = null);

    abstract public function specificRowValidation($row, $notification = null, $isPosting = false);

    abstract public function specificRowValidationByFlow($row, $notification = null, $isPosting = false);

    abstract public function addTransactionRow($transactionRow);

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
        $snapshot->flow = $this->movementFlow;
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->wh = $this->warehouse;

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
     * validation header
     *
     * @return Notification
     */
    public function validate($notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

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

        $notification = $this->generalHeaderValidation($notification);

        if ($notification->hasErrors())
            return $notification;

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

        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($this->movementDate)) {
            $notification->addError("Transaction date is not correct or empty");
        } else {

            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $spec1->setCompanyId($this->company);
            if (! $spec1->isSatisfiedBy($this->movementDate)) {
                $notification->addError("Can not post on this date. Period is not created or closed.");
            }
        }

        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($this->docCurrency))
            $notification->addError("Currency is empty or invalid");

        // check movement type
        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->movementType)) {
            $notification->addError("Transaction Type is not correct or empty");
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
                    if ($row->getDocType() !== $this->movementType) {
                        $notification->addError("Transaction Type is inconsistent, Because at least one row has different type");
                        break;
                    }
                }
            }
        }

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
        if ($notification == null)
            $notification = new Notification();

        // allways done
        $notification = $this->generalRowValidation($row, $notification, $isPosting);

        if ($notification->hasErrors())
            return $notification;

        $specificRowValidationByFlowResult = $this->specificRowValidationByFlow($row, $notification, $isPosting);

        if ($specificRowValidationByFlowResult != null) {
            $notification = $specificRowValidationByFlowResult;
        }

        $specificRowValidationResult = $this->specificRowValidation($row, $notification, $isPosting);
        if ($specificRowValidationResult != null)
            $notification = $specificRowValidationResult;

        return $notification;
    }

    /**
     *
     * @param TransactionRow $row
     *            ;
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
        if ($this->sharedSpecificationFactory == null) {
            $notification->addError("Validators is not found");
            return $notification;
        }

        if ($row->getMvUuid() !== $this->uuid) {
            $notification->addError("transaction id not match");
            return $notification;
        }

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();
        $spec->setCompanyId($this->company);
        if (! $spec->isSatisfiedBy($row->getItem()))
            $notification->addError("Item not exits in the company #" . $this->company);

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($row->getDocQuantity()))
            $notification->addError("Quantity is not valid! " . $row->getDocQuantity());

        return $notification;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    /**
     *
     * @return \Inventory\Domain\AbstractSpecificationFactory
     */
    public function getDomainSpecificationFactory()
    {
        return $this->domainSpecificationFactory;
    }

    /**
     *
     * @param \Application\Domain\Shared\Specification\AbstractSpecificationFactory $sharedSpecificationFactory
     */
    public function setSharedSpecificationFactory($sharedSpecificationFactory)
    {
        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
    }

    /**
     *
     * @param \Inventory\Domain\AbstractSpecificationFactory $domainSpecificationFactory
     */
    public function setDomainSpecificationFactory($domainSpecificationFactory)
    {
        $this->domainSpecificationFactory = $domainSpecificationFactory;
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
     * @return \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface
     */
    public function getTransactionRepository()
    {
        return $this->transactionRepository;
    }
    
    /**
     * @param \Inventory\Domain\Warehouse\Transaction\TransactionRepositoryInterface $transactionRepository
     */
    public function setTransactionRepository($transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
    
}