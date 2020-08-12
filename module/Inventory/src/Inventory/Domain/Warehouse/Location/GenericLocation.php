<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\SnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericLocation extends AbstractLocation
{

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new LocationSnapshot());
    }
}