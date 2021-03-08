<?php
namespace HR\Domain\Employee\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Util\Translator;
use HR\Domain\Employee\BaseIndividual;
use HR\Domain\Validator\Employee\AbstractIndividualValidator;
use HR\Domain\Validator\Employee\IndividualValidatorInterface;
use InvalidArgumentException;
use Application\Domain\Util\OutputMessage;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultEmployeeValidator extends AbstractIndividualValidator implements IndividualValidatorInterface
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

            $spec = $this->getDomainSpecificationFactory()->getEmployeeCodeExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "employeeCode" => $rootEntity->getEmployeeCode()
            );

            if ($spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(OutputMessage::error(Translator::translate("Employee code exits"), $rootEntity->getEmployeeCode()));
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

