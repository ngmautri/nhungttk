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
class GLAccountValidator extends AbstractValidator implements RowValidatorInterface
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

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ======= ITEM ==========
            $spec = $this->sharedSpecificationFactory->getGLAccountExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "glAccountId" => $localEntity->getGlAccount()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("GL #%s not exits in the company #%s", $localEntity->getGlAccount(), $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "costCenter" => $localEntity->getCostCenter()
            );
            
            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Cost center #%s not exits in the company #%s", $localEntity->getCostCenter(), $rootEntity->getCompany()));
            }
        } catch (GrCreateException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

