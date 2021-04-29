<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Util\Collection\GenericCollection;
use Inventory\Domain\Warehouse\BaseWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseCollection extends GenericCollection
{

    public function isExits(BaseWarehouse $otherElement)
    {
        $found = $this->exists(function ($key, $element) use ($otherElement) {
            return $otherElement->getWhCode() == $element->getWhCode();
        });
        return $found == false;
    }
}
