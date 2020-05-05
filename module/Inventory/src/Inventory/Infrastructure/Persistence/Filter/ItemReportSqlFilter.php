<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $isFixedAsset;

    public $isInventoryItem;

    public $itemType;

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return \sprintf("ItemReportSqlFilter_%s_%s_%s_%s", $this->isActive, $this->isFixedAsset, $this->isInventoryItem, $this->itemType);
    }

    // ===========================

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     *
     * @return mixed
     */
    public function getIsInventoryItem()
    {
        return $this->isInventoryItem;
    }

    /**
     *
     * @return mixed
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $isFixedAsset
     */
    public function setIsFixedAsset($isFixedAsset)
    {
        $this->isFixedAsset = $isFixedAsset;
    }

    /**
     *
     * @param mixed $isInventoryItem
     */
    public function setIsInventoryItem($isInventoryItem)
    {
        $this->isInventoryItem = $isInventoryItem;
    }

    /**
     *
     * @param mixed $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }
}
