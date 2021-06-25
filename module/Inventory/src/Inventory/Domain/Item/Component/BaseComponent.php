<?php
namespace Inventory\Domain\Item\Component;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Composite\AbstractComposite;
use Inventory\Domain\Item\Composite\BaseComposite;
use Inventory\Domain\Warehouse\Location\BaseLocationSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseComponent extends AbstractComposite
{

    public function equals(BaseComponent $other)
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