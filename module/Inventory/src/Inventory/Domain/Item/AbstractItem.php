<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractItem
{

    /**
     *
     * @var ItemId;
     */
    private $id;

    /**
     *
     * @var string
     */
    private $itemName;

    /**
     *
     * @var ItemSKU
     */
    private $itemSKU;

    /**
     *
     * @var string
     */
    private $itemType;

    /**
     *
     * @var string
     */
    private $monitorMethod;

    /**
     *
     * @var StandardUnit;
     */
    private $standardUnit;

    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    public function setItemSKU($itemSKU)
    {
        $this->itemSKU = $itemSKU;
    }

    public function getStandardUnit()
    {
        return $this->standardUnit;
    }

    public function setStandardUnit($standardUnit)
    {
        $this->standardUnit = $standardUnit;
    }

    public function getItemName()
    {
        return $this->itemName;
    }

    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    // =====================

    /**
     *
     * @return string
     */
    public function getItemType()
    {
        return ItemType::UNKNOWN_ITEM_TYPE;
    }
}