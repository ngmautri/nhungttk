<?php
namespace Procure\Domain\GoodsReceipt\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GenericGR;
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
        if (! $rootEntity instanceof GenericGR) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof GRRow) {
            throw new InvalidArgumentException('GR Row not given!');
        }

        // do verification now

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
                    $localEntity->addError(sprintf("Warehouse is required for inventory item! %s", $localEntity->getItemName()));
                }
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

