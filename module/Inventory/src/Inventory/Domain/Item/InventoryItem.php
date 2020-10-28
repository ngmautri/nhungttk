<?php
namespace Inventory\Domain\Item;

use Inventory\Domain\Item\Contracts\ItemType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class InventoryItem extends GenericItem
{

    public function __construct()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::INVENTORY_ITEM_TYPE);
        $this->setIsStocked(1);
        $this->setIsFixedAsset(0);
        $this->setIsSparepart(1);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\GenericItem::specifyItem()
     */
    public function specifyItem()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::INVENTORY_ITEM_TYPE);
        $this->setIsStocked(1);
        $this->setIsFixedAsset(0);
        $this->setIsSparepart(1);
    }
}