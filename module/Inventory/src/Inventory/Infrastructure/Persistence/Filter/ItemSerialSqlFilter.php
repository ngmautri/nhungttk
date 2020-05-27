<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialSqlFilter implements SqlFilterInterface
{

    public $itemId;

    /**
     *
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     *
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        $format = 'ItemSerialSqlFilter_itemId_%s';
        return \sprintf($format, $this->getItemId());
    }
}
