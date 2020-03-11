<?php
namespace Procure\Domain\Service;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;

/**
 * PO Specification Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POSpecService
{

    protected $sharedSpecificationFactory;

    protected $fxService;

    /**
     *
     * @param AbstractSpecificationFactory $sharedSpecificationFactory
     * @param FXServiceInterface $fxService
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
     * @param GenericPO $rootEntity
     * @param boolean $isPosting
     */
    public function doGeneralHeaderValiation(GenericPO $rootEntity, $isPosting = false)
    {
        if (! $rootEntity instanceof GenericPO) {
            throw new \Procure\Domain\Exception\InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->sharedSpecificationFactory == null) {
            $rootEntity->addError("Validators is not found");
            return;
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

            // ==== CK Contract DATE =======
            if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($rootEntity->getContractDate())) {
                $rootEntity->addError("Contract date is not correct or empty");
            }

            // ==== CK CONTRACT NO =======
            if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($rootEntity->getContractNo())) {
                $rootEntity->addError("Contract number is not correct or empty");
            }

            // ===== DOC CURRENCY =======
            if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency())) {
                $rootEntity->addError(sprintf("Doc Currency is empty or invalid! %s", $rootEntity->getDocCurrency()));
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
            if ($rootEntity->getWarehouse() !== null) {

                $spec1 = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $rootEntity->getWarehouse()
                );
                if (! $spec1->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Warehouse not found!C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $$rootEntity->getCreatedBy()));
            }

            // ===== INCOTERM =======
            if ($rootEntity->getIncoterm() !== null) {

                $spec = $this->sharedSpecificationFactory->getIncotermSpecification();
                $subject = array(
                    "incotermId" => $rootEntity->getIncoterm()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(sprintf("Incoterm not found!C#%s", $rootEntity->getIncoterm()));
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
                $rootEntity->addError(sprintf("Payment term not found! #%s", $rootEntity->getPaymentTerm()));
            }

            // ===== USER ID =======
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
    }

    /**
     *
     * @param GenericPO $rootEntity
     * @param PORow $localEntity
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     */
    public function doGeneralRowValiation(GenericPO $rootEntity, PORow $localEntity)
    {
        if (! $rootEntity instanceof GenericPO) {
            throw new \Procure\Domain\Exception\InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof PORow) {
            throw new \Procure\Domain\Exception\InvalidArgumentException('PORow not given!');
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
            $localEntity->addError(sprintf("Item #%s not exits in the company #%s", $localEntity->getItem(), $rootEntity->getCompany()));
        }

        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

        // ======= QUANTITY ==========
        if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
            $localEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
        }

        // ======= UNIT PRICE ==========
        if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
            $localEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
        }

        // ======= CONVERSION FACTORY ==========
        if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
            $localEntity->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
        }
        // ======= EXW PRICE ==========
        if (! $spec->isSatisfiedBy($localEntity->getExwUnitPrice())) {
            // $notification->addError("Exw Unit price is not valid! " . $localEntity->getExwUnitPrice());
        }

    }

    /**
     *
     * @return \Procure\Domain\Service\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }
}
