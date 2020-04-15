<?php
namespace Procure\Domain\AccountPayable\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;
use Application\Domain\Util\Translator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InvoiceAndPaymentTermValidator extends AbstractValidator implements HeaderValidatorInterface
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
            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

            // ==== INVOICE NUMBER =======
            if (! $spec->isSatisfiedBy($rootEntity->getDocNumber())) {
                $rootEntity->addError(Translator::translate("Invoice number is required!"));
            }

            $spec = $this->sharedSpecificationFactory->getDateSpecification();

            // ==== INVOICE DATE =======
            if (! $spec->isSatisfiedBy($rootEntity->getDocDate())) {
                $rootEntity->addError(Translator::translate("Good Receipt date is not correct or empty"));
            }

            // ==== PAYMENT TERM =======
            $spec = $this->sharedSpecificationFactory->getPaymentTermSpecification();
            $subject = array(
                "paymentTermId" => $rootEntity->getPmtTerm()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(Translator::translate("Payment term is required!"));
            }
         
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

