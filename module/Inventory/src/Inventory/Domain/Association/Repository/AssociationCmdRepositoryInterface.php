<?php
namespace Inventory\Domain\Association\Repository;

use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Contracts\Repository\CmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AssociationCmdRepositoryInterface extends CmdRepositoryInterface
{

    public function store(BaseAssociation $rootEntity, $generateSysNumber = True);
}
