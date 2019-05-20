<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Inventory\Domain\Item\Specification\InventoryItemSpecification;
use Inventory\Domain\Exception\LogicException;
use Ramsey;

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
        $this->item = new InventoryItem();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::validate()
     */
    public function validate()
    {
        $spec1 = new ItemSpecification();
        $spec2 = new InventoryItemSpecification();

        // check invariants
        $spec = $spec1->andSpec($spec2);

        if (! $spec->isSatisfiedBy($this->item)) {
            throw new LogicException("Can not create inventory-item");
        }
    }
}