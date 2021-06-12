<?php
namespace Inventory\Domain\Item\Batch;

use Inventory\Domain\Item\Variant\AbstractVariant;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseBatch extends AbstractVariant
{

    public function equals(BaseBatch $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getVariantCodeVO()->equals($other->getVariantCodeVO());
    }
}
