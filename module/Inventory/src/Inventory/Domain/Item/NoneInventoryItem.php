<?php
namespace Inventory\Domain\Item;

use Inventory\Domain\Item\Contracts\ItemType;

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
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::NONE_INVENTORY_ITEM_TYPE);
        $this->setIsStocked(0);
        $this->setIsFixedAsset(0);
        $this->setIsSparepart(0);
    }
}