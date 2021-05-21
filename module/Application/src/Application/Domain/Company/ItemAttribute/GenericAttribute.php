<?php
namespace Application\Domain\Company\ItemAttribute;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericAttribute extends BaseAttribute
{

    public static function constructFromDB(AttributeSnapshot $snapshot)
    {
        Assert::isInstanceOf($snapshot, AttributeSnapshot::class, "AttributeSnapshot is required!");

        $instance = new GenericAttribute();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    /**
     *
     * @param BaseAttributeGroup $rootDoc
     * @param AttributeSnapshot $snapshot
     * @return \Application\Domain\Company\ItemAttribute\GenericAttribute
     */
    public static function createFromSnapshot(BaseAttributeGroup $rootDoc, AttributeSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, BaseAttributeGroup::class, "BaseAttributeGroup required!");
        Assert::isInstanceOf($snapshot, AttributeSnapshot::class, "AttributeSnapshot is required!");

        $instance = new GenericAttribute();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}
