<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item\Filter;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemQueryFilter implements QueryFilterInterface
{

    protected $isFixedAsset;

    protected $isInventoryItem;

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

    public function __toString()
    {}
}
