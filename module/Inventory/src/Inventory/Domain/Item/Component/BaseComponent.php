<?php
namespace Inventory\Domain\Item\Component;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Warehouse\Location\BaseLocationSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseComponent extends AbstractComponent
{

    public function equals(BaseComponent $other)
    {
        if ($other == null) {
            return false;
        }

        return \strtolower(trim($this->getId())) == \strtolower(trim($other->getId()));
    }

  
    /**
     * 
     * @return \Inventory\Domain\Item\Component\BaseComponentSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new BaseComponentSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }
}