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
class PoRowValidator extends AbstractValidator implements RowValidatorInterface
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
            throw new InvalidArgumentException('GR Row not given!');
        }

        // do verification now

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        try {

            $subject = [
                "vendorId" => $rootEntity->getVendor(),
                "poRowId" => $localEntity->getPoRow()
            ];

            $spec = $this->getProcureSpecificationFactory()->getPoRowSpecification();
            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("PO %s does not belong to vendor %s", $localEntity->getPoRow(), $rootEntity->getVendor()));
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

