<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Exception\LogicException;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Ramsey;

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
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::validate()
     */
    public function validate()
    {
        $spec = new ItemSpecification();

        if (! $spec->isSatisfiedBy($this->item)) {
            throw new LogicException("Can not create Service");
        }
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