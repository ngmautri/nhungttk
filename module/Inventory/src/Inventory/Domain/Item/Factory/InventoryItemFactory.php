<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Inventory\Domain\Item\Specification\InventoryItemSpecification;
use Inventory\Domain\Exception\LogicException;
use Ramsey;
use Application\Notification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItem()
     */
    public function createItem()
    {
        $item = new InventoryItem();
        $this->item = $item;
        return $item;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::validate()
     */
    public function validate()
    {
        /*
         * $spec1 = new ItemSpecification();
         * $spec2 = new InventoryItemSpecification();
         *
         * // check invariants
         * $spec = $spec1->andSpec($spec2);
         *
         * if (! $spec->isSatisfiedBy($this->item)) {
         * throw new LogicException("Can not create inventory-item");
         * }
         */
        $notification = new Notification();

        if ($this->item == null) {
            throw new InvalidArgumentException("Item is empty");
        }
        

        /**
         *
         * @var AbstractItem $item
         */
        $item = $this->item;
         
        if ($this->isNullOrBlank($item->getItemName())) {
            $err = "Item name is null or empty";
            $notification->addError($err);
        } else {

            if (preg_match('/[#$%*@]/', $item->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
        }

        if ($this->isNullOrBlank($item->getItemSku())) {
            $err = "Item SKU is null or empty";
            $notification->addError($err);
        }

        if ($item->getStandardUom() == null) {
            $err = "Item unit is null or empty";
            $notification->addError($err);
        }
        
        return $notification;
    }
    
    
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::specifyItem()
     */
    public function specifyItem()
    {
        $this->item->itemType = "ITEM";
        
    }
}