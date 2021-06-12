<?php
namespace Application\Domain\Company\PostingPeriod;

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
class GenericPostingPeriod extends BasePostingPeriod
{

    public static function constructFromDB(GenericPostingPeriod $snapshot)
    {
        Assert::isInstanceOf($snapshot, GenericPostingPeriod::class, "AttributeSnapshot is required!");

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
