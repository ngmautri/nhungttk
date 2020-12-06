<?php
namespace Inventory\Domain\Transaction\GI\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CostCenterValidator extends AbstractValidator implements RowValidatorInterface
{

    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException('BaseRow Row not given!');
        }

        // do verification now

        Try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            $spec = $this->sharedSpecificationFactory->getCostCenterExitsSpecification();

            // ======= COST CENTER =========
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "costCenter" => $localEntity->getCostCenter()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Cost center needed, but not found #%s company #%s", $localEntity->getCostCenter(), $rootEntity->getCompany()));
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

