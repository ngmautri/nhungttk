<?php
namespace Procure\Domain\PurchaseOrder\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\AbstractDoc;
use Procure\Domain\AbstractRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\RowValidatorInterface;

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
     * @see \Procure\Domain\Validator\RowValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity, AbstractRow $localEntity)
    {
        if (! $rootEntity instanceof GenericPO) {
            throw new InvalidArgumentException(\sprintf('Root entity not given!%s', __METHOD__));
        }

        if (! $localEntity instanceof PORow) {
            throw new InvalidArgumentException(\sprintf('Local entity not given!%s', __METHOD__));
        }

        // do verification now

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

        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();

        // ======= QUANTITY ==========
        if (! $spec->isSatisfiedBy($localEntity->getDocQuantity())) {
            $localEntity->addError("Quantity is not valid! " . $localEntity->getDocQuantity());
        }

        // ======= UNIT PRICE ==========
        if (! $spec->isSatisfiedBy($localEntity->getDocUnitPrice())) {
            $localEntity->addError("Unit price is not valid! " . $localEntity->getDocUnitPrice());
        }

        // ======= CONVERSION FACTORY ==========
        if (! $spec->isSatisfiedBy($localEntity->getStandardConvertFactor())) {
            $localEntity->addError("Standard convert factor is not valid! " . $localEntity->getStandardConvertFactor());
        }
        // ======= EXW PRICE ==========
        if (! $spec->isSatisfiedBy($localEntity->getExwUnitPrice())) {
            // $notification->addError("Exw Unit price is not valid! " . $localEntity->getExwUnitPrice());
        }

        if (! $localEntity->getTaxRate() == null) {
            if (! $spec->isSatisfiedBy($localEntity->getTaxRate())) {
                $localEntity->addError("Tax Rate is not valid! " . $localEntity->getTaxRate());
            }
        }
    }
}

