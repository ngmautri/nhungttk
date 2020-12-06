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
class PositivePriceValidator extends AbstractValidator implements RowValidatorInterface
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

        try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */
            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            // ======= Unit Price ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
                $localEntity->addError("Unit price is not valid! It should be greater 0." . $localEntity->getDocUnitPrice());
            }
        } catch (RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

