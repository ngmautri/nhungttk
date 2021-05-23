<?php
namespace Inventory\Domain\Item\Collection;

use Application\Domain\Util\Collection\GenericCollection;
use Inventory\Domain\Item\Variant\BaseVariant;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemVariantCollection extends GenericCollection
{

    public function isExits(BaseVariant $otherElement)
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
