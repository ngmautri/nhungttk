<?php
namespace Inventory\Domain\Item\Collection;

use Application\Domain\Util\Collection\GenericCollection;
use Inventory\Domain\Item\Picture\BasePicture;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemPictureCollection extends GenericCollection
{

    public function isExits(BasePicture $otherElement)
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
