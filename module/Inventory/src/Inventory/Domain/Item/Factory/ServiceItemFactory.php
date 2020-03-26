<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Exception\LogicException;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Ramsey;
use Application\Notification;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ServiceItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItem()
     */
    public function createItem()
    {
        $this->item = new ServiceItem();
        return $this->item;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::specifyItem()
     */
    public function specifyItem()
    {
        $item = $this->item;
        $item->setItemType("SERVICE");
    }
}