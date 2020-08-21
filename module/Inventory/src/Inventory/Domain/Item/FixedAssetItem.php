<?php
namespace Inventory\Domain\Item;

use Application\Notification;
use Inventory\Domain\Item\Contracts\ItemType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FixedAssetItem extends GenericItem
{

    public function __construct()
    {
        $this->setItemType("ITEM");
        $this->setItemTypeId(ItemType::FIXED_ASSET_ITEM_TYPE);
        $this->setIsFixedAsset(1);
        $this->setIsStocked(0);
        $this->setIsSparepart(0);
    }

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
        $this->setItemTypeId(ItemType::FIXED_ASSET_ITEM_TYPE);
        $this->setIsFixedAsset(1);
        $this->setIsStocked(0);
        $this->setIsSparepart(0);
    }
}