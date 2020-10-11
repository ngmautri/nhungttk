<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\FixedAssetItem;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class FixedAssetItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItem()
     */
    public function createItem()
    {
        $this->item = new FixedAssetItem();
        return $this->item;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::specifyItem()
     */
    public function specifyItem()
    {
        /**
         *
         * @var GenericItem $item ;
         */
        $item = $this->item;
        $item->setItemType("ITEM");
    }
}