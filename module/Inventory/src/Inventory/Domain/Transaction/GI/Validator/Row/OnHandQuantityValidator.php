<?php
namespace Inventory\Domain\Transaction\GI\Validator\Row;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Validator\Warehouse\Transaction\RowValidatorInterface;
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

        if (! $localEntity instanceof TrxRow) {
            throw new InvalidArgumentException('TrxRow Row not given!');
        }

        // do verification now

        Try {

            $specDomain = $this->sharedSpecificationFactory()->getOnhandQuantitySpecification();

            $subject = array(
                "warehouseId" => $localEntity->warehouse,
                "itemId" => $localEntity->getItem(),
                "movementDate" => $localEntity->movementDate,
                "docQuantity" => $localEntity->getDocQuantity()
            );

            if (! $specDomain->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Can not issue this quantity %s on %s (WH #%s ,  Item #%s)", $localEntity->getDocQuantity(), $this->movementDate, $this->warehouse, $localEntity->getItem()));
            }
        } catch (\RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

