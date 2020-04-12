<?php
namespace Procure\Domain\APInvoice\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrDateAndWarehouseValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new GrInvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {
            // ==== GR DATE =======
            $spec = $this->sharedSpecificationFactory->getDateSpecification();

            if (! $spec->isSatisfiedBy($rootEntity->getGrDate())) {
                $rootEntity->addError("Good Receipt date is not correct or empty");
            }

            // ===== WAREHOUSE =======            
            if ($rootEntity->getWarehouse() == null) {
                $rootEntity->addError("Source warehouse is not set");
            } else {

                $spec1 = $this->getSharedSpecificationFactory()->getWarehouseExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $rootEntity->getWarehouse()
                );
                if (! $spec1->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $rootEntity->getCreatedBy()));
            }
        } catch (GrCreateException $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

