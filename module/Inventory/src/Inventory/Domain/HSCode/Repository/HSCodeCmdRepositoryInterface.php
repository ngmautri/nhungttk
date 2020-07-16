<?php
namespace Inventory\Domain\HSCode\Repository;

use Inventory\Domain\Contracts\Repository\CmdRepositoryInterface;
use Inventory\Domain\HSCode\BaseHSCode;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface HSCodeCmdRepositoryInterface extends CmdRepositoryInterface
{

    public function store(BaseHSCode $rootEntity, $generateSysNumber = True);
}
