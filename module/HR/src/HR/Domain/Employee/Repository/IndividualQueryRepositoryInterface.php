<?php
namespace HR\Domain\Employee\Repository;

use HR\Domain\Contracts\Repository\QueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface IndividualQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);

    public function getRootEntityByTokenId($id, $token);

    public function getRootEntityById($id);

    public function getIndividualTypeById($id);

    public function getByEmployeeCode($code);
}
