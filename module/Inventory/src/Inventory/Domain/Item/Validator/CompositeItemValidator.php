<?php
namespace Inventory\Domain\Item\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Validator\Item\AbstractItemValidator;
use Inventory\Domain\Validator\Item\ItemValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompositeItemValidator extends AbstractItemValidator implements ItemValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Item\ItemValidatorInterface::validate()
     */
    public function validate(AbstractItem $rootEntity)
    {
        if (! $rootEntity instanceof AbstractItem) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getItemSku())) {
                $rootEntity->addError("Item SKU is null or empty. It is required for inventory item.");
            }

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
            $spec1 = $this->sharedSpecificationFactory->getMeasureUnitExitsSpecification();
            $spec1->setCompanyId($rootEntity->getCompany());

            if (! $spec1->isSatisfiedBy($rootEntity->getStandardUom())) {
                $format = 'Measurement unit is invalid or empty. It is required for inventory item! %s';
                $rootEntity->addError(\sprintf($format, $rootEntity->getStandardUom()));
            }

            if (! $spec1->isSatisfiedBy($rootEntity->getStockUom())) {
                $rootEntity->addError("Inventory measurement unit is invalid");
            }

            if (! $spec->isSatisfiedBy($rootEntity->getStockUomConvertFactor())) {
                $rootEntity->addError("Inventory measurement conversion factor invalid!");
            }

            if ($spec1->isSatisfiedBy($rootEntity->getPurchaseUom()) && ! $spec->isSatisfiedBy($rootEntity->getPurchaseUomConvertFactor())) {
                $rootEntity->addError("Purchase measurement unit is set, but no conversion factor!");
            }

            if ($spec1->isSatisfiedBy($rootEntity->getSalesUom()) && ! $spec->isSatisfiedBy($rootEntity->getSalesUomConvertFactor())) {
                $rootEntity->addError("Sales measurement unit is set, but no conversion factor!");
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}

