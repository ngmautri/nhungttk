<?php
namespace Procure\Domain\AccountPayable\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AccountPayable\GenericAP;
use Procure\Domain\Exception\InvalidArgumentException;
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
        if (! $rootEntity instanceof GenericAP) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {
            // ==== GR DATE =======
            $spec = $this->sharedSpecificationFactory->getDateSpecification();

            if (! $spec->isSatisfiedBy($rootEntity->getGrDate())) {
                $rootEntity->addError("Good Receipt date is not correct or empty!", $rootEntity->getGrDate());
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
                if (! $spec1->isSatisfiedBy($subject)) {
                    $rootEntity->addError(sprintf("Wareouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $rootEntity->getCreatedBy()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

