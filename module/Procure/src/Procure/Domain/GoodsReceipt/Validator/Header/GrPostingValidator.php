<?php
namespace Procure\Domain\GoodsReceipt\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrPostingValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new GrInvalidArgumentException('Root entity not given!');
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
                $rootEntity->addError(sprintf("Can not post on this date (Date %s CompanyID %s). Period is not created or closed. ", $rootEntity->getGrDate(), $rootEntity->getCompany()));
            }
        } catch (GrCreateException $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

