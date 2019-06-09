<?php
namespace Inventory\Domain\Item;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ServiceItem extends GenericItem
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
        $this->setItemType("SERVICE");
        $this->setItemTypeId(ItemType::SERVICE_ITEM_TYPE);
    }
}