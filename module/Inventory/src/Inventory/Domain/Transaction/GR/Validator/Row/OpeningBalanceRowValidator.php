<?php
namespace Inventory\Domain\Transaction\GR\Validator\Row;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorInterface;
use InvalidArgumentException;

/**
 * Opening Balance
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpeningBalanceRowValidator extends AbstractValidator implements RowValidatorInterface
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

        Try {
            // do verification now

            $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "itemId" => $localEntity->getItem()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $localEntity->addError(sprintf("Item #%s not exits in the company #%s", $localEntity->getItem(), $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

            // ======= QUANTITY ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
                $localEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
            }

            // ======= UNIT PRICE ==========
            if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
                $localEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
            }
        } catch (\Exception $e) {
            $localEntity->addError($e->getMessage());
        }
    }
}

