<?php
namespace Procure\Domain\GoodsReceipt\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof GRRow) {
            throw new InvalidArgumentException('GR Row not given!');
        }

        // do verification now

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        try {
            if ($localEntity->getPoRow() != null) {
                $subject = [
                    "vendorId" => $rootEntity->getVendor(),
                    "poRowId" => $localEntity->getPoRow()
                ];

                $spec = $this->getProcureSpecificationFactory()->getPoRowSpecification();
                if (! $spec->isSatisfiedBy($subject)) {
                    $localEntity->addError(sprintf("[GR] PO %s not of vendor %s", $localEntity->getPoRow(), $rootEntity->getVendor()));
                }
            }
        } catch (OperationFailedException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

