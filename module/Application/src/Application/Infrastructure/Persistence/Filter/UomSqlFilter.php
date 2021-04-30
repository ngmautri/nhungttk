<?php
namespace Application\Infrastructure\Persistence\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomSqlFilter extends DefaultListSqlFilter
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
        $format = 'AssociationSqlFilter_itemId_%s';
        return \sprintf($format, $this->getItemId());
    }
}
