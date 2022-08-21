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
class EmployeeCodeValidator extends AbstractIndividualValidator implements IndividualValidatorInterface
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

        if ($this->getDomainSpecificationFactory() == null) {
            throw new InvalidArgumentException('Domain specification required!');
        }

        if ($this->getDomainSpecificationFactory()->getIndividualSpecificationFactory() == null) {
            throw new InvalidArgumentException('Specification for individual required!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            if ($rootEntity->getEmployeeCode() != null) {

                $specFactory = $this->getDomainSpecificationFactory()->getIndividualSpecificationFactory();
                $spec = $specFactory->getEmployeeCodeExitsSpecification();

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "employeeCode" => $rootEntity->getEmployeeCode()
                );

                if ($spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(OutputMessage::error(Translator::translate("Employee Code exits"), $rootEntity->getEmployeeCode()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

