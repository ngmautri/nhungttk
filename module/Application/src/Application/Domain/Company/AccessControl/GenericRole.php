<?php
namespace Application\Domain\Company\AccessControl;

use Application\Domain\Company\ItemAttribute\AttributeSnapshot;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\ItemAttribute\GenericAttribute;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericRole extends BaseRole
{

    public static function constructFromDB(GenericRole $snapshot)
    {
        Assert::isInstanceOf($snapshot, GenericRole::class, "GenericRole is required!");

        $instance = new GenericAttribute();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }

    public static function createFromSnapshot(BaseAttributeGroup $rootDoc, AttributeSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, BaseAttributeGroup::class, "BaseAttributeGroup required!");
        Assert::isInstanceOf($snapshot, AttributeSnapshot::class, "AttributeSnapshot is required!");

        $instance = new GenericAttribute();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);

        return $instance;
    }
}
