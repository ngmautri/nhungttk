<?php
namespace Procure\Domain\AccountPayable\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericAP) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof APRow) {
            throw new InvalidArgumentException('AP Row not given!');
        }

        // do verification now

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        try {

            if ($localEntity->getPrRow() != null) {

                $subject = [
                    "prRowId" => $localEntity->getPrRow(),
                    "warehouseId" => $localEntity->getWarehouse()
                ];

                $spec = $this->getProcureSpecificationFactory()->getPrRowSpecification();
                if (! $spec->isSatisfiedBy($subject)) {
                    $localEntity->addError(sprintf("[AP] PR and Warehouse not matched!", $localEntity->getPrRow(), $rootEntity->getWarehouse()));
                }
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

