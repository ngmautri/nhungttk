<?php
namespace Inventory\Domain\Item\Variant;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericVariantAttribute extends BaseVariantAttribute
{

    public static function constructFromDB(VariantAttributeSnapshot $snapshot)
    {
        Assert::isInstanceOf($snapshot, VariantAttributeSnapshot::class, "VariantAttributeSnapshot is required!");

        $instance = new GenericVariantAttribute();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param BaseVariant $rootDoc
     * @param VariantAttributeSnapshot $snapshot
     * @return \Inventory\Domain\Item\Variant\GenericVariantAttribute
     */
    public static function createFromSnapshot(BaseVariant $rootDoc, VariantAttributeSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, BaseVariant::class, "BaseVariant required!");
        Assert::isInstanceOf($snapshot, VariantAttributeSnapshot::class, "VariantAttributeSnapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        Assert::notNull($instance, "Can not create GenericVariantAttribute");

        return $instance;
    }
}
