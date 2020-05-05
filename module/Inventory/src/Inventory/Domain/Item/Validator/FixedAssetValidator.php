<?php
namespace Inventory\Domain\Item\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Validator\AbstractItemValidator;
use Inventory\Domain\Validator\ItemValidatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItemValidator extends AbstractItemValidator implements ItemValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\ItemValidatorInterface::validate()
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

            if ($spec->isSatisfiedBy($this->getItemSku())) {
                $rootEntity->addError("Item SKU is null or empty. It is required for inventory item.");
            }

            $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
            $spec1 = $this->sharedSpecificationFactory->getMeasureUnitExitsSpecification();
            $spec1->setCompanyId($this->company);

            if (! $spec1->isSatisfiedBy($this->getStandardUom())) {
                $rootEntity->addError("Measurement unit is invalid or empty. It is required for inventory item.");
            }

            if (! $spec1->isSatisfiedBy($this->getStockUom())) {
                $rootEntity->addError("Inventory measurement unit is invalid");
            }

            if (! $spec->isSatisfiedBy($this->getStockUomConvertFactor())) {
                $rootEntity->addError("Inventory measurement conversion factor invalid!");
            }

            if ($spec1->isSatisfiedBy($this->getPurchaseUom()) && ! $spec->isSatisfiedBy($this->getPurchaseUomConvertFactor())) {
                $rootEntity->addError("Purchase measurement unit is set, but no conversion factor!");
            }

            if ($spec1->isSatisfiedBy($this->getSalesUom()) && ! $spec->isSatisfiedBy($this->getSalesUomConvertFactor())) {
                $rootEntity->addError("Sales measurement unit is set, but no conversion factor!");
            }
              
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
    
    
}

