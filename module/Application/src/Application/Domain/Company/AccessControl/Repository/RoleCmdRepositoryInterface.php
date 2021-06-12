<?php
namespace Application\Domain\Company\AccessControl\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\AccessControl\BaseRole;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface RoleCmdRepositoryInterface
{

    public function storeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false);

    public function removeRole(BaseCompany $rootEntity, BaseRole $localEntity, $isPosting = false);
}
