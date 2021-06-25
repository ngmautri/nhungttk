<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Component\BaseComponent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemComponentCmdRepositoryInterface
{

    public function storeComponenent(GenericItem $rootEntity, BaseComponent $localEntity, $generateSysNumber = True);

    public function storeComponentCollection(GenericItem $rootEntity, $generateSysNumber = True);

    public function removeComponent(GenericItem $rootEntity, BaseComponent $localEntity, $isPosting = false);
}
