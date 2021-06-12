<?php
namespace Inventory\Domain\Item\Picture;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BasePicture extends AbstractPicture
{

    public function equals(BasePicture $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getToken()->equals($other->getToken());
    }
}
