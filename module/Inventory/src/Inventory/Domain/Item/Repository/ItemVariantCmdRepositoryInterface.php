<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemVariantCmdRepositoryInterface
{

    public function storeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True);

    public function storeVariantCollection(GenericItem $rootEntity, $generateSysNumber = True);

    public function storeWholeVariant(GenericItem $rootEntity, BaseVariant $localEntity, $generateSysNumber = True);

    public function storeAttribute(BaseVariant $rootEntity, $localEntity, $generateSysNumber = True);

    public function removeAttribute(BaseVariant $rootEntity, BaseVariantAttribute $localEntity, $isPosting = false);
}
