<?php
namespace Procure\Domain\QuotationRequest\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Util\Translator;
use Procure\Domain\AbstractDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\QuotationRequest\GenericQR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultHeaderValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (! $rootEntity instanceof GenericQR) {
            throw new InvalidArgumentException(\sprintf('Root entity not given!%s', __METHOD__));
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError(Translator::translate("Company not exits. #" . $rootEntity->getCompany()));
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

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();
            // ==== DOC NUMBER =======
            if ($spec->isSatisfiedBy($rootEntity->getDocNumber())) {
                $rootEntity->addError(Translator::translate(\sprintf("Quotation number is required!%s", $rootEntity->getDocNumber())));
            }

            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getDocDate())) {
                $rootEntity->addError("Document Date is not correct or empty");
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
                "paymentTermId" => $rootEntity->getPmtTerm()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Payment term not found! #%s", $rootEntity->getPmtTerm()));
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(Translator::translate("User is not identified for this transaction. #" . $rootEntity->getCreatedBy()));
            }

            if ($rootEntity->getLastchangeBy() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastchangeBy()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(Translator::translate("User is not identified for this transaction. #" . $rootEntity->getLastchangeBy()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

