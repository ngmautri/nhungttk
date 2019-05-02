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