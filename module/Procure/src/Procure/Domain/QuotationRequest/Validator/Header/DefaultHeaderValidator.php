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

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

            // ==== DOC NUMBER =======
            if ($spec->isSatisfiedBy($rootEntity->getDocNumber())) {
                $rootEntity->addError(Translator::translate(\sprintf("PR number is required!%s", $rootEntity->getDocNumber())));
            }

            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getSubmittedOn())) {
                $rootEntity->addError("PR Date is not correct or empty");
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

