<?php
namespace Inventory\Domain\Item\Serial;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseSerial extends AbstractSerial

{

    public function equals(BaseSerial $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getId()->equals($other->getId());
    }
}
