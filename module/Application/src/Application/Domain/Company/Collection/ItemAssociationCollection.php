<?php
namespace Application\Domain\Company\Collection;

use Application\Domain\Company\ItemAssociation\BaseAssociation;
use Application\Domain\Util\Collection\GenericCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAssociationCollection extends GenericCollection
{

    public function isExits(BaseAssociation $otherElement)
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
