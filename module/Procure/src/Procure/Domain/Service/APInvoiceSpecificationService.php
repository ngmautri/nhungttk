<?php
namespace Procure\Domain\Service;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Procure\Domain\APInvoice\GenericAPInvoice;
use Procure\Domain\APInvoice\APInvoiceRow;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceSpecificationService
{

    protected $sharedSpecificationFactory;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
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
     * @param GenericAPInvoice $aggregateRoot
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralHeaderValiation(GenericAPInvoice $rootEntity, Notification $notification)
    {
        if ($notification == null)
            $notification = new Notification();
        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        if ($notification->hasErrors())
            return $notification;

        // do verification now

        // ==== check company =======
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
            $notification->addError("Company not exits. #" . $rootEntity->getCompany());
        }

        // ===== Vendor =======
        if ($rootEntity->getVendor() == null) {
            $notification->addError("Warehouse is not set");
        } else {
            $spec = $this->sharedSpecificationFactory->getVendorExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "vendorId" => $rootEntity->getVendor()
            );
            if (! $spec->isSatisfiedBy($subject))
                $notification->addError(sprintf("Vendor not found !C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getVendor()));
        }

        // ==== INVOICE DATE =======
        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getInvoiceDate())) {
            $notification->addError("Invoice date is not correct or empty");
        }

        // ==== Posting date =======
        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getPostingDate())) {
            $notification->addError("Posting date is not correct or empty");
        } else {
            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getPostingDate()
            );
            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post on this date. Period is not created or closed." . $rootEntity->getPostingDate());
            }
        }

        // ==== Good receipt date =======
        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getGrDate())) {
            $notification->addError("Posting date is not correct or empty");
        } else {
            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getGrDate()
            );
            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post goods receipt on this date. Period is not created or closed." . $rootEntity->getGrDate());
            }
        }

        // ===== Doc Currency =======
        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency()))
            $notification->addError("Currency is empty or invalid");

        // ===== Local Currency =======
        if ($rootEntity->getLocalCurrency() == null) {
            $notification->addError("Local currency is not set");
        } else {
            $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getLocalCurrency()))
                $notification->addError("Local currency not exits..." . $rootEntity->getLocalCurrency());
        }

        // ===== Warehouse =======
        if ($rootEntity->getWarehouse() == null) {
            $notification->addError("Warehouse is not set");
        } else {
            $spec1 = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "warehouseId" => $rootEntity->getWarehouse()
            );
            if (! $spec1->isSatisfiedBy($subject))
                $notification->addError(sprintf("Warehouse not found!C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $$rootEntity->getCreatedBy()));
        }

        // ===== INCOTERM =======
        if ($rootEntity->getIncoterm2() !== null) {

            $spec = $this->sharedSpecificationFactory->getIncotermSpecification();
            $subject = array(
                "incotermId" => $rootEntity->getIncoterm2()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError(sprintf("Incoterm not found!C#%s, WH#%s, U#%s", $rootEntity->getIncoterm2()));
            }

            if ($rootEntity->getIncotermPlace() == null or $rootEntity->getIncotermPlace() == "") {
                $notification->addError(sprintf("Incoterm place not set"));
            }
        }

        // ===== PAYMENT TERM =======
        $spec = $this->sharedSpecificationFactory->getPaymentTermSpecification();
        $subject = array(
            "paymentTermId" => $rootEntity->getPaymentTerm()
        );
        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError(sprintf("Payment term not found!C#%s", $rootEntity->getPaymentTerm()));
        }

        return $notification;
    }

    /**
     *
     * @param GenericAPInvoice $rootEntity
     * @param APInvoiceRow $localEntity
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralRowValiation(GenericAPInvoice $rootEntity, APInvoiceRow $localEntity, Notification $notification)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($localEntity == null)
            return $notification;

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($localEntity->getId() !== $rootEntity->getUuid()) {
            $notification->addError("invoice id not match");
            return $notification;
        }

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "itemId" => $localEntity->getItem()
        );

        if (! $spec->isSatisfiedBy($subject))
            $notification->addError("Item not exits in the company #" . $rootEntity->getCompany());

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())){
            $notification->addError("Quantity is not valid! " . $localEntity->getDocQuantity());}
                        
       return $notification;
    }
}
