<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Shared\Specification\AbstractSpecificationForCompany;
use Inventory\Application\Specification\Doctrine\OnhandQuantitySpecification;
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
    

    abstract public function prePost();
    
    abstract public function afterPost();
    
    abstract public function post();

    abstract public function specificValidation($notification = null);

    /**
     *
     * @param TransactionRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    abstract public function specificRowValidation($row, $notification = null, $isPosting = false);

    /**
     *
     * @param TransactionRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    abstract public function specificRowValidationByFlow($row, $notification = null, $isPosting = false);

    abstract public function addTransactionRow($transactionRow);

    /**
     *
     * @param TransactionRowDTO $transactionRowDTO
     */
    public function addRow($transactionRow)
    {
        $this->transactionRows[] = $transactionRow;
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

        $notification = $this->generalValidation($notification);

        if ($notification->hasErrors())
            return $notification;

        $specificValidationResult = $this->specificValidation($notification);
        if ($specificValidationResult instanceof Notification)
            $notification = $specificValidationResult;

        if ($notification->hasErrors())
            return $notification;

        foreach ($this->transactionRows as $row) {

            $checkRowResult = $this->validateRow($row, $notification);

            if ($checkRowResult !== null)
                $notification = $checkRowResult;
        }

        return $notification;
    }

    /**
     *
     * @param Notification $notification
     * @return string|\Application\Notification
     */
    protected function generalValidation($notification = null, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->domainSpecificationFactory == null || $this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        if (count($this->transactionRows) == 0)
            $notification->addError("Transaction has no line");

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

        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($this->currency))
            $notification->addError("Currency is empty or invalid");

        // check movement type
        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->movementType)) {
            $notification->addError("Transaction Type is not correct or empty");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($this->movementType, $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }
        }

        // check warehouse currency
        if ($this->warehouse == null) {
            $notification->addError("Source warehouse is not set");
        } else {

            $spec1 = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
            $spec1->setCompanyId($this->company);
            if (! $spec1->isSatisfiedBy($this->warehouse))
                $notification->addError("Source Warehouse not exits..." . $this->warehouse);
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

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();
        $spec->setCompanyId($this->company);
        if (! $spec->isSatisfiedBy($row->getItem()))
            $notification->addError("Item not exits in the company #" . $this->company);

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($row->getDocQuantity()))
            $notification->addError("Quantity is not valid!");

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
     * @return \Inventory\Domain\Service\ValuationServiceInterface
     */
    public function getValuationService()
    {
        return $this->valuationService;
    }

    /**
     * @param \Inventory\Domain\Service\ValuationServiceInterface $valuationService
     */
    public function setValuationService($valuationService)
    {
        $this->valuationService = $valuationService;
    }

}