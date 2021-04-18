<?php
namespace Application\Domain\Company\AccountChart\Repository;

use Application\Domain\Contracts\Repository\CompanySqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ChartQueryRepositoryInterface
{

    public function getById($id, CompanySqlFilterInterface $filter);

    public function getByUUID($uuid, CompanySqlFilterInterface $filter);
}
