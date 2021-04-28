<?php
namespace Application\Domain\Company\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\Department\DepartmentSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanyCmdRepositoryInterface
{

    public function store(GenericCompany $company);

    // ================================================================
    // Delegation
    // ================================================================
    public function storeDeparment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function storeAccountChart(BaseCompany $rootEntity, BaseChart $localEntity);

    public function storeWholeAccountChart(BaseCompany $rootEntity, BaseChart $localEntity);

    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);

    public function removeAccountChart(BaseCompany $rootEntity, BaseChart $localEntity);

    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);

    public function storeWarehouse(GenericCompany $company, $warehouse);

    public function storePostingPeriod(GenericCompany $company);
}
