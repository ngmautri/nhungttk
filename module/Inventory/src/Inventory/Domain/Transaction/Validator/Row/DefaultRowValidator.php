<?php
namespace Inventory\Domain\Transaction\Validator\Row;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
        }

        if (! $localEntity instanceof TrxRow) {
            throw new InvalidArgumentException('TrxRow Row not given!');
        }

        try {

            /**
             *
             * @var AbstractSpecification $spec ;
             */

            // ======= ITEM ==========
            $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "itemId" => $localEntity->getItem()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Item #%s not exits in the company #%s", $localEntity->getItem(), $rootEntity->getCompany()));
            }
        } catch (RuntimeException $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

