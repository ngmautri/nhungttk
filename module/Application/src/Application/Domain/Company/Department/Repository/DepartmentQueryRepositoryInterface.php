<?php
namespace Application\Domain\Company\Department\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface DepartmentQueryRepositoryInterface
{

    public function getById($id);

    public function getByUUID($uuid);
}
