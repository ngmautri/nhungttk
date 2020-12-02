<?php
namespace Application\Domain\Company\Repository;

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

    public function getPostingPeriod($periodId);
}
