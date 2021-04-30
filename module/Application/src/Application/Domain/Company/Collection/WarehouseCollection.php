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
        if ($this->isEmpty()) {
            return FALSE;
        }

        $found = false;

        $found = $this->exists(function ($key, $element) use ($otherElement) {

            // var_dump($otherElement->equals($element));
            return $otherElement->equals($element);
        });

        return $found;
    }
}
