<?php
namespace Inventory\Domain\Item\Component;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericComponent extends BaseComponent
{

    /**
     *
     * @param LocationSnapshot $snapshot
     * @return \Inventory\Domain\Warehouse\Location\GenericLocation
     */
    public static function constructFromDB(LocationSnapshot $snapshot)
    {
        Assert::isInstanceOf($snapshot, LocationSnapshot::class, "AccountSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param BaseWarehouse $rootDoc
     * @param LocationSnapshot $snapshot
     * @return \Inventory\Domain\Warehouse\Location\GenericLocation
     */
    public static function createFromSnapshot(BaseWarehouse $rootDoc, LocationSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, BaseWarehouse::class, "BaseWarehouse required!");
        Assert::isInstanceOf($snapshot, LocationSnapshot::class, "LocationSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}