<?php
namespace Inventory\Domain\Transaction\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NoneNegativeQuantityValidator extends AbstractValidator implements RowValidatorInterface
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

        // do verification now

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getNoneNegativeNumberSpecification();

            // ======= QUANTITY ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
                $localEntity->addError("Quantity is not valid! It should be greated or equal 0" . $localEntity->getDocQuantity());
            }

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            // ======= CONVERSION FACTORY ==========
            if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
                $localEntity->addError("Convert factor is not valid! #" . $localEntity->getConversionFactor());
            }
        } catch (RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

