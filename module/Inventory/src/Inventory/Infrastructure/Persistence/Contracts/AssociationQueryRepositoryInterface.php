<?php
namespace Inventory\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AssociationQueryRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);
}
