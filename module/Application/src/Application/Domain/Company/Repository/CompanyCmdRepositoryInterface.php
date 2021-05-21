<?php
namespace Application\Domain\Company\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\GenericCompany;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\ItemAttribute\Repository\ItemAttributeCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanyCmdRepositoryInterface extends WhCmdRepositoryInterface, ChartCmdRepositoryInterface, ItemAttributeCmdRepositoryInterface
{

    public function storeCompany(GenericCompany $company);

    // ================================================================
    // Delegation
    // ================================================================
    public function storeDeparment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function removeDepartment(BaseCompany $rootEntity, DepartmentSnapshot $localSnapshot, $isPosting = false);

    public function storePostingPeriod(GenericCompany $company);
}
