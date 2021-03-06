<?php
namespace Procure\Domain\PurchaseRequest\Validator\Row;

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
class WarehouseValidator extends AbstractValidator implements RowValidatorInterface
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

        Try {
            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ===== WAREHOUSE =======
            if ($localEntity->getIsInventoryItem() == 1) {

                $spec = $this->getSharedSpecificationFactory()->getWarehouseExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $localEntity->getWarehouse()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $f = "Warehouse is required for inventory item! %s-%s";
                    $localEntity->addError(sprintf($f, $localEntity->getItemName(), $localEntity->getWarehouse()));
                }
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

