<?php
namespace Inventory\Domain\Item\Composite;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseComposite extends AbstractComposite
{

    public function equals(BaseComposite $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getLocationCode())) == \strtolower(trim($other->getLocationCode()));
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Location\BaseLocationSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseLocationSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}