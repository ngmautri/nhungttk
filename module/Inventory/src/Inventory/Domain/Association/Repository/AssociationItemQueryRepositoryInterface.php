<?php
namespace Inventory\Domain\Association\Repository;

use Inventory\Domain\Contracts\Repository\QueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AssociationItemQueryRepositoryInterface extends QueryRepositoryInterface
{

    public function getVersion($id, $token = null);

    public function getVersionArray($id, $token = null);

    public function getRootEntityByTokenId($id, $token);

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);
}
