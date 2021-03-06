<?php
namespace Inventory\Domain\Item;

use Inventory\Domain\Item\Contracts\ItemType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ServiceItem extends GenericItem
{

    public function __construct()
    {
        $this->setItemType("SERVICE");
        $this->setItemTypeId(ItemType::SERVICE_ITEM_TYPE);
        $this->setIsFixedAsset(0);
        $this->setIsStocked(0);
        $this->setIsSparepart(0);
        $this->setMonitoredBy(null); // ignore monitoring smethod.
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("SERVICE");
        $this->setItemTypeId(ItemType::SERVICE_ITEM_TYPE);
        $this->setIsFixedAsset(0);
        $this->setIsStocked(0);
        $this->setIsSparepart(0);
    }
}