<?php
namespace Application\Domain\Company\Brand\Repository;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface BrandRepositoryInterface
{

    public function getRootByMemberId($memberId);

    public function getById($id);

    public function getByUUID($uuid);

    public function getList(CompanySqlFilterInterface $filter);
}
