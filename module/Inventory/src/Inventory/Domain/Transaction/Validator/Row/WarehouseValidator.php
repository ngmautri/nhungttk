<?php
namespace Inventory\Domain\Transaction\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use InvalidArgumentException;
use RuntimeException;

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
     * @see \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException('TrxRow not given!');
        }

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ===== WAREHOUSE =======
            if (! $localEntity->getIsInventoryItem() && $localEntity->getIsFixedAsset() == 1) {
                $localEntity->addError(\sprintf("Item is not kept stocked! %s", $localEntity->getItemName()));
            }

            $spec = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "warehouseId" => $localEntity->getWarehouse()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Warehouse is required for inventory item! %s", $localEntity->getItemName()));
            }
        } catch (RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

