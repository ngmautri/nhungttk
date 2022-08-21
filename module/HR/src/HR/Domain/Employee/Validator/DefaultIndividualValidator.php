<?php
namespace HR\Domain\Employee\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Util\OutputMessage;
use Application\Domain\Util\Translator;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Validator\Employee\AbstractIndividualValidator;
use HR\Domain\Validator\Employee\IndividualValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultIndividualValidator extends AbstractIndividualValidator implements IndividualValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Validator\Employee\IndividualValidatorInterface::validate()
     */
    public function validate(BaseIndividual $rootEntity)
    {
        if (! $rootEntity instanceof BaseIndividual) {
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
                $rootEntity->addError(OutputMessage::error(Translator::translate("Company not exits."), $rootEntity->getCompany()));
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(OutputMessage::error(Translator::translate("User is not identified for this transaction"), $rootEntity->getCreatedBy()));
            }

            if ($rootEntity->getLastchangeBy() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastchangeBy()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(OutputMessage::error(Translator::translate("User is not identified for this transaction"), $rootEntity->getLastchangeBy()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

