<?php
namespace Application\Domain\Company\Repository;

use Application\Domain\Contracts\Repository\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface CompanyQueryRepositoryInterface
{

    public function findAll();

    public function getById($id);

    public function getByUUID($uuid);

    // Delegation
    public function getPostingPeriod($periodId);

    public function getDepartmentList(SqlFilterInterface $filter);
}
