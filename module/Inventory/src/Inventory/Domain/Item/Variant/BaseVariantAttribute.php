<?php
namespace Inventory\Domain\Item\Variant;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseVariantAttribute extends AbstractVariant
{

    public function equals(BaseVariantAttribute $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getId())) == \strtolower(trim($other->getId()));
    }

    public function makeSnapshot()
    {
        $snapshot = new VariantAttributeSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}
