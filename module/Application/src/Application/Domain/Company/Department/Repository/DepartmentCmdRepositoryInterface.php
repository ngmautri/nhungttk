<?php
namespace Application\Domain\Company\Department\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Department\BaseDepartment;
use Application\Domain\Company\Department\DepartmentSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface DepartmentCmdRepositoryInterface
{

    public function storeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function store(BaseDepartment $rootEntity, $isPosting = false);

    public function remove(BaseDepartment $rootEntity, $isPosting = false);
}
