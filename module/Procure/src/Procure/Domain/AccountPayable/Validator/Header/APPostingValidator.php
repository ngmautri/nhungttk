<?php
namespace Procure\Domain\AccountPayable\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
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
class APPostingValidator extends AbstractValidator implements HeaderValidatorInterface
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
            // ==== GR DATE =======
            $spec = $this->getSharedSpecificationFactory()->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getGrDate()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Can not post goods receipt on this date (Date %s CompanyID %s). Period is not created or closed. ", $rootEntity->getGrDate(), $rootEntity->getCompany()));
            }

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getPostingDate()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Can not post accouting entry on this date (Date %s CompanyID %s). Period is not created or closed. ", $rootEntity->getPostingDate(), $rootEntity->getCompany()));
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

