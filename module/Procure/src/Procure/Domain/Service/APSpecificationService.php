<?php
namespace Procure\Domain\Service;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Procure\Domain\APInvoice\APDocRow;
use Procure\Domain\APInvoice\GenericAPDoc;
use Application\Domain\Shared\Specification\AbstractSpecification;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APSpecificationService
{

    protected $sharedSpecificationFactory;

    protected $fxService;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, FXServiceInterface $fxService)
    {
        if ($sharedSpecificationFactory == null) {
            throw new InvalidArgumentException("Shared Specification not found");
        }

        if ($fxService == null) {
            throw new InvalidArgumentException("FX service not found");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->fxService = $fxService;
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
     * @param GenericAPDoc $rootEntity
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralHeaderValiation(GenericAPDoc $rootEntity, Notification $notification, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }
        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->sharedSpecificationFactory == null) {
            $notification->addError("Validators is not found");
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        // do verification now

        try {

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $notification->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            // ===== CK VENDOR =======
            if ($rootEntity->getVendor() == null) {
                $notification->addError("Vendor is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getVendorExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "vendorId" => $rootEntity->getVendor()
                );
                if (! $spec->isSatisfiedBy($subject))
                    $notification->addError(sprintf("Vendor not found !C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getVendor()));
            }

            // ==== CK INVOICE DATE =======
            if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getInvoiceDate())) {
                $notification->addError("Invoice date is not correct or empty");
            }

            if ($isPosting) {

                $spec = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "movementDate" => $rootEntity->getPostingDate()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $notification->addError("Can not post on this date. Period is not created or closed." . $rootEntity->getPostingDate());
                }

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "movementDate" => $rootEntity->getGrDate()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $notification->addError("Can not post goods receipt on this date. Period is not created or closed." . $rootEntity->getGrDate());
                }
            }

            // ===== DOC CURRENCY =======
            if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency())) {
                $notification->addError("Doc Currency is empty or invalid");
            }

            // ===== LOCAL CURRENCY =======
            if ($rootEntity->getLocalCurrency() == null) {
                $notification->addError("Local currency is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
                if (! $spec->isSatisfiedBy($rootEntity->getLocalCurrency()))
                    $notification->addError("Local currency not exits..." . $rootEntity->getLocalCurrency());
            }

            // ===== WAREHOUSE =======
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
                    $notification->addError(sprintf("Incoterm not found!C#%s", $rootEntity->getIncoterm2()));
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

            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("User is not identified for this transaction. #" . $rootEntity->getCreatedBy());
            }
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     *
     * @param GenericAPDoc $rootEntity
     * @param APDocRow $localEntity
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralRowValiation(GenericAPDoc $rootEntity, APDocRow $localEntity, Notification $notification)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($localEntity == null) {
            $notification->addError("row not found");
            return $notification;
        }

        // do verification now

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        // ======= ITEM ==========
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "itemId" => $localEntity->getItem()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("Item not exits in the company #" . $localEntity->getItem());
        }

        // ======= QUANTITY ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
            $notification->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
        }

        // ======= UNIT PRICE ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
            $notification->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
        }

        // ======= CONVERSION FACTORY ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
            $notification->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
        }

        // ======= GL ACCOUNT ==========
        $spec = $this->sharedSpecificationFactory->getGLAccountExitsSpecification();

        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "glAccountId" => $localEntity->getGlAccount()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("GL account is not valid! " . $localEntity->getGlAccount());
        }

        // ======= COST CENTER ==========
        $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();
        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "costCenter" => $localEntity->getCostCenter()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("Cost center is not valid! " . $localEntity->getCostCenter());
        }
        
        return $notification;
    }
    
    /**
     * @return \Procure\Domain\Service\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }

}
