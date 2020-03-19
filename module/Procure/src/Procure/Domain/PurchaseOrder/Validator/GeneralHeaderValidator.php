<?php
namespace module\Procure\src\Procure\Domain\PurchaseOrder\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\Exception\PoInvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralHeaderValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \module\Procure\src\Procure\Domain\PurchaseOrder\Validator\HeaderValidatorInterface::validate()
     */
    public function validate($rootEntity)
    {
        if (! $rootEntity instanceof GenericPO) {
            throw new PoInvalidArgumentException('Root entity not given!');
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

            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getContractDate())) {
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
}
