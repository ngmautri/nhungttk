<?php
namespace Procure\Domain\GoodsReceipt\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;

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
            throw new GrInvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof GRRow) {
            throw new GrInvalidArgumentException('GR Row not given!');
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
            if(!$spec->isSatisfiedBy($subject)){
                $localEntity->addError(sprintf("PO %s does not belong to vendor %s",$localEntity->getPoRow(),$rootEntity->getVendor()));
            }
            
           
        } catch (GrCreateException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

