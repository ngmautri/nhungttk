<?php
namespace User\Domain\User\Repository;

use User\Domain\User\GenericUser;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface UserCmdRepositoryInterface
{

    public function storeUser(GenericUser $rootEntity, $generateSysNumber = false, $isPosting = false);
}
