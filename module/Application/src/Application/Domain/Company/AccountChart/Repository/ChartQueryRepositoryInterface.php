<?php
namespace Application\Domain\Company\AccountChart\Repository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ChartQueryRepositoryInterface
{

    public function getById($id);

    public function getByUUID($uuid);
}
