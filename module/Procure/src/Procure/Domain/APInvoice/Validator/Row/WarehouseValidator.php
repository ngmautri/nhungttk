<?php
namespace Procure\Domain\APInvoice\Validator\Row;

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

            // ===== WAREHOUSE =======
            if (! $localEntity->getWarehouse() == null) {
                $spec = $this->getSharedSpecificationFactory()->getWarehouseExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $localEntity->getWarehouse()
                );
                if (! $spec->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $rootEntity->getCompany(), $localEntity->getWarehouse(), $rootEntity->getCreatedBy()));
            }
            
        } catch (GrCreateException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

