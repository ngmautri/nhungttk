<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Inventory\Infrastructure\Persistence\Contracts\AssociationCmdRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationCmdRepositoryImpl extends AbstractDoctrineRepository implements AssociationCmdRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {}
}
