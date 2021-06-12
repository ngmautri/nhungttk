<?php
namespace Inventory\Domain\Item\Collection;

use Application\Domain\Util\Collection\GenericCollection;
use Inventory\Domain\Item\Batch\BaseBatch;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemBatchCollection extends GenericCollection
{

    public function isExits(BaseBatch $otherElement)
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
