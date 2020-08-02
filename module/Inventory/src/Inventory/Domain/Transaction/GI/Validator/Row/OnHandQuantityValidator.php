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
class OnHandQuantityValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException('BaseRow Row not given!');
        }

        Try {
            // do verification now

            /**
             *
             * @var AbstractSpecification $spec ;
             */
            $spec = $this->getDomainSpecificationFactory()->getOnhandQuantitySpecification();

            $subject = [
                "itemId" => $localEntity->getItem(),
                "warehouseId" => $rootEntity->getWarehouse(),
                "movementDate" => $rootEntity->getMovementDate(),
                "docQuantity" => $localEntity->getDocQuantity()
            ];

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(\sprintf("Onhand quantity is not enough %s %s %s. Pleae review quantity and warehouse !", $rootEntity->getPostingDate(), $localEntity->getDocQuantity(), $rootEntity->getWarehouse()));
            }
        } catch (\RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

