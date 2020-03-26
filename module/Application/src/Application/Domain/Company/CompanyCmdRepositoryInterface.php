<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\Department;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface CompanyRepositoryInterface
{

    public function findAll();

    public function getById($id);

    public function getByUUID($uuid);

    public function store(Company $company);

    public function addDeparment(Company $company, Department $department);

    public function addWarehouse(Company $company, $warehouse);

    public function addPostingPeriod(Company $company);

    public function getPostingPeriod($periodId);
}
