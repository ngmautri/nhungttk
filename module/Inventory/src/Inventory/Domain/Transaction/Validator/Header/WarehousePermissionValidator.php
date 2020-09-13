<?php
namespace Inventory\Domain\Transaction\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehousePermissionValidator extends AbstractValidator implements HeaderValidatorInterface
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

        try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ===== WAREHOUSE ACL =======

            $spec = $this->sharedSpecificationFactory->getWarehouseACLSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "warehouseId" => $rootEntity->getWarehouse(),
                "userId" => $rootEntity->getCreatedBy()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("No permision on WH %s for UserId %s", $rootEntity->getWarehouse(), $rootEntity->getCreatedBy()));
            }
        } catch (RuntimeException $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

