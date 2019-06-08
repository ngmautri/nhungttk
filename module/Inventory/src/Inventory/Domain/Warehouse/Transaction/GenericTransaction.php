<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Application\Domain\Shared\Specification\AbstractSpecification;

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

    abstract public function post();

    /**
     *
     * @param TransactionRowDTO $transactionRowDTO
     */
    public function addRow($transactionRowDTO)
    {
        $row = new TransactionRow();
        $snapshot = TransactionRowSnapshotAssembler::createSnapshotFromDTO($transactionRowDTO);
        $row->makeFromSnapshot($snapshot);
        $this->transactionRows[] = $row;
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
        if ($notification == null) {
            $notification = new Notification();
        }

        $notification = $this->generalValidation($notification);

        if ($this->specificValidation($notification) !== null) {
            return $this->specificValidation($notification);
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
        if ($this->domainSpecificationFactory == null or $this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        // do verification now

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits..." . $this->company);
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
                $notification->addError("Can not post on this date");
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

            $spec = $this->domainSpecificationFactory->getWarehouseExitsSpecification();
            if (! $spec->isSatisfiedBy($this->warehouse))
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

    abstract public function specificValidation($notification = null);

    abstract public function specificRowValidation($notification = null);

    abstract public function addTransactionRow($transactionRowDTO);

    /**
     * validation header
     *
     * @return Notification
     */
    public function validateRow($row, $notification, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        $notification = $this->generalRowValidation();

        if ($this->specificRowValidation($notification) !== null) {
            return $this->specificValidation($notification);
        }

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
        
        
            if ($row == null){
                return $notification
            }
                 

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->domainSpecificationFactory == null or $this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        // do verification now

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits..." . $this->company);
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
                $notification->addError("Can not post on this date");
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

            $spec = $this->domainSpecificationFactory->getWarehouseExitsSpecification();
            if (! $spec->isSatisfiedBy($this->warehouse))
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
}