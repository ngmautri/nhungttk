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
        if (! $spec->isSatisfiedBy($trx->getCompany())) {
            $notification->addError("Company not exits. #" . $trx->getCompany());
        }

        // check movement type
        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($trx->getMovementType())) {
            $notification->addError("Transaction Type is not set.");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($trx->getMovementType(), $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }
            // check if transactition type is consistent - when change header
            if (count($trx->getTransactionRows()) > 0) {
                foreach ($trx->getTransactionRows() as $row) {
                    /**
                     *
                     * @var TransactionRow $row ;
                     */
                    if ($row->getTransactionType() !== $trx->getMovementType()) {
                        $notification->addError("Transaction Type is inconsistent, because at least one row has different type");
                        break;
                    }
                }
            }
        }

        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($trx->getMovementDate())) {
            $notification->addError("Transaction date is not correct or empty");
        } else {
            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $trx->getCompany(),
                "movementDate" => $trx->getMovementDate()
            );
            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post on this date. Period is not created or closed." . $trx->getMovementDate());
            }
        }

        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($trx->getDocCurrency()))
            $notification->addError("Currency is empty or invalid");

        // check warehouse
        if ($trx->getWarehouse() == null) {
            $notification->addError("Source warehouse is not set");
        } else {
            $spec1 = $this->sharedSpecificationFactory->getWarehouseACLSpecification();
            $subject = array(
                "companyId" => $trx->getCompany(),
                "warehouseId" => $trx->getWarehouse(),
                "userId" => $trx->getCreatedBy()
            );
            if (! $spec1->isSatisfiedBy($subject))
                $notification->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $trx->getCompany(), $trx->getWarehouse(), $trx->getCreatedBy()));
        }

        // check local currency
        if ($trx->getLocalCurrency() == null) {
            $notification->addError("Local currency is not set");
        } else {
            $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($trx->getLocalCurrency()))
                $notification->addError("Local currency not exits..." . $trx->getLocalCurrency());
        }
        return $notification;
    }

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralRowValiation(GenericTransaction $trx, TransactionRow $row, Notification $notification)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($row == null)
            return $notification;

        /**
         *
         * @var AbstractSpecificationForCompany $spec ;
         */
        if ($row->getMvUuid() !== $trx->getUuid()) {
            $notification->addError("transaction id not match");
            return $notification;
        }

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

        $subject = array(
            "companyId" => $trx->getCompany(),
            "itemId" => $row->getItem()
        );

        if (! $spec->isSatisfiedBy($subject))
            $notification->addError("Item not exits in the company #" . $trx->getCompany());

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($row->getDocQuantity()))
            $notification->addError("Quantity is not valid! " . $row->getDocQuantity());

        return $notification;
    }
}
