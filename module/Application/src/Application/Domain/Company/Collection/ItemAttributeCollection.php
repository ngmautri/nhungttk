<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeCollection extends GenericCollection
{

    public function isExits(BaseAttribute $otherElement)
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
