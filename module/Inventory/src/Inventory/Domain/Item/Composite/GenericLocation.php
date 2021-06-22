<?php
namespace Inventory\Domain\Item\Composite
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Webmozart\Assert\Assert;
use Inventory\Domain\Item\Composite\BaseComposite;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericComposite extends BaseComposite
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