<?php
namespace Inventory\Domain\Service;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionSpecificationService
{

    protected $sharedSpecificationFactory;

    protected $domainSpecificationFactory;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param \Inventory\Domain\AbstractSpecificationFactory $domainSpecificationFactory
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, \Inventory\Domain\AbstractSpecificationFactory $domainSpecificationFactory)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($domainSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->domainSpecificationFactory = $domainSpecificationFactory;
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
     * @param GenericTransaction $trx
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralHeaderValiation(GenericTransaction $trx, Notification $notification)
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
     *
     * @param TransactionRow $row
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralRowValiation(TransactionRow $row, Notification $notification)
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
}
