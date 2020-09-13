<?php
namespace Inventory\Domain\Transaction\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxPostingValidator extends AbstractValidator implements HeaderValidatorInterface
{

    public function validate(AbstractTrx $rootEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {
            // ==== Trx DATE =======
            $spec = $this->getSharedSpecificationFactory()->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "movementDate" => $rootEntity->getMovementDate()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $f = 'Can not post on this date (Date #%s CompanyID#%s). Period is not created or closed!';
                $rootEntity->addError(sprintf($f, $rootEntity->getMovementDate(), $rootEntity->getCompany()));
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

