<?php
namespace Inventory\Domain\Transaction\GI\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIForReturnPOValidator extends AbstractValidator implements RowValidatorInterface
{

    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException('BaseRow Row not given!');
        }

        try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            if (! $spec->isSatisfiedBy($localEntity->getGr())) {
                $f = 'PO-GR not found!';
                $localEntity->addError(sprintf($f, $localEntity->getGr()));
            }

            $spec = $this->getDomainSpecificationFactory()->getOnhandQuantityOfMovementSpecification();

            $subject = [
                "itemId" => $localEntity->getItem(),
                "movementId" => $rootEntity->getRelevantMovementId(),
                "docQuantity" => $localEntity->getQuantity()
            ];

            if (! $spec->isSatisfiedBy($subject)) {
                $f = 'Good issue not posible! Please check stock quantity!';
                $localEntity->addError(sprintf($f, $localEntity->getQuantity()));
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

