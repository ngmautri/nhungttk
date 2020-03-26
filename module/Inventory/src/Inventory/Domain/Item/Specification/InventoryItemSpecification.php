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
    public function __construct(AbstractItem $subject = null)
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

        if ($subject->getItemType() !== ItemType::INVENTORY_ITEM_TYPE) {
            return false;
        }

        // unit is required.
        if ($subject->itemSku == null) {
            return false;
        }

        // unit is required.
        if ($subject->standardUom == null) {
            return false;
        }

        return true;
    }
}