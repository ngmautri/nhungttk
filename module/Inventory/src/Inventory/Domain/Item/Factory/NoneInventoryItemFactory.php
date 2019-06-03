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
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\NoneInventoryItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NoneInventoryItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItem()
     */
    public function createItem()
    {
        $this->item = new NoneInventoryItem();
        return $this->item;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::specifyItem()
     */
    public function specifyItem()
    {
        /**
         * @var GenericItem $item ;
         */
        $item =  $this->item;
        $item->setItemType("ITEM");
    }
}