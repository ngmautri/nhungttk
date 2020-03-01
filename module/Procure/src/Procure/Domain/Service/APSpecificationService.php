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
    public function doGeneralHeaderValiation(GenericAPDoc $rootEntity, $isPosting = false)
    {
        if (! $rootEntity instanceof GenericAPDoc) {
            throw new \Procure\Domain\Exception\InvalidArgumentException("GenericAPDoc not given");
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->sharedSpecificationFactory == null) {
            $rootEntity->addError("Validators is not found");
            return $rootEntity;
        }

        // do verification now

        try {

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            // ===== CK VENDOR =======
            if ($rootEntity->getVendor() == null) {
                $rootEntity->addError("Vendor is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getVendorExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "vendorId" => $rootEntity->getVendor()
                );
                if (! $spec->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Vendor not found !C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getVendor()));
            }

            // ==== CK INVOICE DATE =======
            if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getInvoiceDate())) {
                $rootEntity->addError("Invoice date is not correct or empty");
            }

            if ($isPosting) {

                $spec = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "movementDate" => $rootEntity->getPostingDate()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("Can not post on this date. Period is not created or closed." . $rootEntity->getPostingDate());
                }

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "movementDate" => $rootEntity->getGrDate()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("Can not post goods receipt on this date. Period is not created or closed." . $rootEntity->getGrDate());
                }
            }

            // ===== DOC CURRENCY =======
            if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency())) {
                $rootEntity->addError("Doc Currency is empty or invalid");
            }

            // ===== LOCAL CURRENCY =======
            if ($rootEntity->getLocalCurrency() == null) {
                $rootEntity->addError("Local currency is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
                if (! $spec->isSatisfiedBy($rootEntity->getLocalCurrency()))
                    $rootEntity->addError("Local currency not exits..." . $rootEntity->getLocalCurrency());
            }

            // ===== WAREHOUSE =======
            if ($rootEntity->getWarehouse() == null) {
                $rootEntity->addError("Warehouse is not set");
            } else {
                $spec1 = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $rootEntity->getWarehouse()
                );
                if (! $spec1->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Warehouse not found!C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $$rootEntity->getCreatedBy()));
            }

            // ===== INCOTERM =======
            if ($rootEntity->getIncoterm2() !== null) {

                $spec = $this->sharedSpecificationFactory->getIncotermSpecification();
                $subject = array(
                    "incotermId" => $rootEntity->getIncoterm2()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(sprintf("Incoterm not found!C#%s", $rootEntity->getIncoterm2()));
                }

                if ($rootEntity->getIncotermPlace() == null or $rootEntity->getIncotermPlace() == "") {
                    $rootEntity->addError(sprintf("Incoterm place not set"));
                }
            }

            // ===== PAYMENT TERM =======
            $spec = $this->sharedSpecificationFactory->getPaymentTermSpecification();
            $subject = array(
                "paymentTermId" => $rootEntity->getPaymentTerm()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Payment term not found!C#%s", $rootEntity->getPaymentTerm()));
            }

            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError("User is not identified for this transaction. #" . $rootEntity->getCreatedBy());
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }

        return $rootEntity;
    }

    /**
     *
     * @param GenericAPDoc $rootEntity
     * @param APDocRow $localEntity
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doGeneralRowValiation(GenericAPDoc $rootEntity, APDocRow $localEntity,$isPosting = false)
    {
        if (! $rootEntity instanceof GenericAPDoc) {
            throw new \Procure\Domain\Exception\InvalidArgumentException("GenericAPDoc not given");
        }

        if (! $localEntity instanceof APDocRow) {
            throw new \Procure\Domain\Exception\InvalidArgumentException("APDocRow not given");
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
            $rootEntity->addError("Item not exits in the company #" . $localEntity->getItem());
        }

        // ======= QUANTITY ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
            $rootEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
        }

        // ======= UNIT PRICE ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
            $rootEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
        }

        // ======= CONVERSION FACTORY ==========
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
            $rootEntity->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
        }

        // ======= GL ACCOUNT ==========
        $spec = $this->sharedSpecificationFactory->getGLAccountExitsSpecification();

        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "glAccountId" => $localEntity->getGlAccount()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $rootEntity->addError("GL account is not valid! " . $localEntity->getGlAccount());
        }

        // ======= COST CENTER ==========
        $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();
        $subject = array(
            "companyId" => $rootEntity->getCompany(),
            "costCenter" => $localEntity->getCostCenter()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $rootEntity->addError("Cost center is not valid! " . $localEntity->getCostCenter());
        }
        
        return $rootEntity;
    }
    
    /**
     * @return \Procure\Domain\Service\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }

}
