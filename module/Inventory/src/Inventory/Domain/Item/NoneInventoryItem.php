<?php
namespace Inventory\Domain\Item;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NoneInventoryItem extends GenericItem
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specificValidation()
     */
    public function specificValidation(Notification $notification = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::NONE_INVENTORY_ITEM_TYPE);
    }
}