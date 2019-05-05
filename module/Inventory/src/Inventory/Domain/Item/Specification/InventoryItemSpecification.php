<?php
namespace Inventory\Domain\Item\Specification;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\ItemType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItemSpecification extends AbstractSpecification
{

    /**
     *
     * @var AbstractItem
     */
    private $subject;

    /**
     *
     * @param AbstractItem $subject
     */
    public function __construct(AbstractItem $subject)
    {
        $this->subject = $subject;
    }

   /**
    * 
    * @param AbstractItem $subject
    * @return boolean
    */
    public function isSatisfiedBy($subject)
    {
        if ($subject == null)
            return false;

        if ($subject->getItemType() !== ItemType::INVENTORY_ITEM_TYPE)
            return false;

        return true;
    }
}