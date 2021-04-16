<?php
namespace Application\Domain\Company\Repository;

use Application\Domain\Company\GenericCompany;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanyCmdRepositoryInterface
{

    public function store(GenericCompany $company);

    public function storeDeparment(GenericCompany $company, $department);

    public function removeDepartment(GenericCompany $company, $department);

    public function storeWarehouse(GenericCompany $company, $warehouse);

    public function storePostingPeriod(GenericCompany $company);
}
