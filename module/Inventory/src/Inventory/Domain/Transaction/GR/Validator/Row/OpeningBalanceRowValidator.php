<?php
namespace Inventory\Domain\Transaction\GR\Validator\Row;

use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use InvalidArgumentException;

/**
 * Opening Balance
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OpeningBalanceRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof TrxRow) {
            throw new InvalidArgumentException('TrxRow Row not given!');
        }

        Try {
            // do verification now

            $spec = $this->sharedSpecificationFactory->getNoneNegativeNumberSpecification();

            // ======= QUANTITY ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
                $localEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
            }

            // ======= UNIT PRICE ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
                $localEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

