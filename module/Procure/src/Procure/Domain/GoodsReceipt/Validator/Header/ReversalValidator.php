<?php
namespace Procure\Domain\GoodsReceipt\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ReversalValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {
            $spec = $this->getSharedSpecificationFactory()->getCanPostOnDateSpecification();

            // ==== POSTING DATE =======
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getReversalDate()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Can not post reservel accouting entry on this date (Date %s CompanyID %s). Period is not created or closed. ", $rootEntity->getReversalDate(), $rootEntity->getCompany()));
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

