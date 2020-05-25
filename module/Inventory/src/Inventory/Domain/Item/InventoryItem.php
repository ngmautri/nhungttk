<?php
namespace Inventory\Domain\Item;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Shared\Specification\AbstractSpecificationForCompany;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItem extends GenericItem
{

    public function __construct()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::INVENTORY_ITEM_TYPE);
        $this->setIsStocked(1);
        $this->setIsFixedAsset(0);
        $this->setIsSparepart(1);
    }

    public function specificValidation(Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        /**
         *
         * @var AbstractSpecification $spec
         * @var AbstractSpecificationForCompany $spec1
         */

        $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

        if ($spec->isSatisfiedBy($this->getItemSku())) {
            $notification->addError("Item SKU is null or empty. It is required for inventory item.");
        }

        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        $spec1 = $this->sharedSpecificationFactory->getMeasureUnitExitsSpecification();
        $spec1->setCompanyId($this->company);

        if (! $spec1->isSatisfiedBy($this->getStandardUom())) {
            $notification->addError("Measurement unit is invalid or empty. It is required for inventory item.");
        }

        if (! $spec1->isSatisfiedBy($this->getStockUom())) {
            $notification->addError("Inventory measurement unit is invalid");
        }

        if (! $spec->isSatisfiedBy($this->getStockUomConvertFactor())) {
            $notification->addError("Inventory measurement conversion factor invalid!");
        }

        if ($spec1->isSatisfiedBy($this->getPurchaseUom()) && ! $spec->isSatisfiedBy($this->getPurchaseUomConvertFactor())) {
            $notification->addError("Purchase measurement unit is set, but no conversion factor!");
        }

        if ($spec1->isSatisfiedBy($this->getSalesUom()) && ! $spec->isSatisfiedBy($this->getSalesUomConvertFactor())) {
            $notification->addError("Sales measurement unit is set, but no conversion factor!");
        }

        return $notification;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::INVENTORY_ITEM_TYPE);
        $this->setIsStocked(1);
        $this->setIsFixedAsset(0);
        $this->setIsSparepart(1);
    }
}