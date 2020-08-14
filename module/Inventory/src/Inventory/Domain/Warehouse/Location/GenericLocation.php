<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\SnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericLocation extends BaseLocation
{

    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new LocationSnapshot());
    }

    public static function makeFromSnapshot(LocationSnapshot $snapshot)
    {
        if (! $snapshot instanceof LocationSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }
}