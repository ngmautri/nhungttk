<?php
namespace Inventory\Domain\Item\Component;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\CompositeItem;
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
     * @param ComponentSnapshot $snapshot
     * @return \Inventory\Domain\Item\Component\GenericComponent
     */
    public static function constructFromDB(ComponentSnapshot $snapshot)
    {
        Assert::isInstanceOf($snapshot, ComponentSnapshot::class, "ComponentSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param CompositeItem $rootDoc
     * @param ComponentSnapshot $snapshot
     * @return \Inventory\Domain\Item\Component\GenericComponent
     */
    public static function createFromSnapshot(CompositeItem $rootDoc, ComponentSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, CompositeItem::class, "CompositeItem required!");
        Assert::isInstanceOf($snapshot, ComponentSnapshot::class, "ComponentSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}