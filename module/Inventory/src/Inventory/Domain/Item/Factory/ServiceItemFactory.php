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
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::validate()
     */
    public function validate()
    {
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

        return $notification;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::specifyItem()
     */
    public function specifyItem()
    {
        $this->item->itemType = "SERVICE";
        
    }



   
}