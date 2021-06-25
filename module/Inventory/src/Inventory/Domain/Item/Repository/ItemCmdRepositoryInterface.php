<?php
namespace Inventory\Domain\Item\Repository;

use Inventory\Domain\Contracts\Repository\CmdRepositoryInterface;
use Inventory\Domain\Item\GenericItem;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemCmdRepositoryInterface extends CmdRepositoryInterface, ItemVariantCmdRepositoryInterface
{

    public function getItemComponentRepository();

    public function getItemVariantRepository();

    public function store(GenericItem $rootEntity, $generateSysNumber = True);
}
