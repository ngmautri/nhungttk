<?php
namespace module\Procure\src\Procure\Domain\PurchaseOrder\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\Exception\PoInvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralRowValidator extends AbstractValidator implements RowValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \module\Procure\src\Procure\Domain\PurchaseOrder\Validator\RowValidatorInterface::validate()
     */
    public function validate($rootEntity, $localEntity)
    {
        if (! $rootEntity instanceof GenericPO) {
            throw new PoInvalidArgumentException('Root entity not given!');
        }

        if (! $localEntity instanceof PORow) {
            throw new PoInvalidArgumentException('PO Row not given!');
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
        if (! $spec->isSatisfiedBy($localEntity->getConversionFactor())) {
            $localEntity->addError("Convert factor is not valid! " . $localEntity->getConversionFactor());
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

