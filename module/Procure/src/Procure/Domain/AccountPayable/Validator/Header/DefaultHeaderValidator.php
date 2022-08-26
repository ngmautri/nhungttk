<?php
namespace Procure\Domain\AccountPayable\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Util\Translator;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
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
        if (! $rootEntity instanceof GenericAP) {
            throw new InvalidArgumentException('Root entity not given!');
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
                $rootEntity->addError(Translator::translate("Vendor is not set"));
            } else {
                $spec = $this->sharedSpecificationFactory->getVendorExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "vendorId" => $rootEntity->getVendor()
                );
                if (! $spec->isSatisfiedBy($subject))
                    $rootEntity->addError(Translator::translate(sprintf("Vendor not found !C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getVendor())));
            }

            // ===== DOC CURRENCY =======
            if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency())) {
                $rootEntity->addError(Translator::translate(sprintf("Doc Currency is empty or invalid! %s", $rootEntity->getDocCurrency())));
            }

            // ===== LOCAL CURRENCY =======
            if ($rootEntity->getLocalCurrency() == null) {
                $rootEntity->addError(Translator::translate("Local currency is not set"));
            } else {
                $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
                if (! $spec->isSatisfiedBy($rootEntity->getLocalCurrency()))
                    $rootEntity->addError(Translator::translate("Local currency not exits..." . $rootEntity->getLocalCurrency()));
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getCompanyUserExSpecification();
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

