<?php
namespace Inventory\Domain\Transaction\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface;
use Exception;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxDateAndWarehouseValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {
            // ==== MV DATE =======
            $spec = $this->getSharedSpecificationFactory()->getDateSpecification();

            if (! $spec->isSatisfiedBy($rootEntity->getMovementDate())) {
                $rootEntity->addError("Movement date is not correct or empty");
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
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

