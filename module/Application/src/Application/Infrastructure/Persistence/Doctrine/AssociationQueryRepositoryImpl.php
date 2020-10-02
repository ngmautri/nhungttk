<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\Contracts\AssociationQueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\AssociationSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AssociationQueryRepositoryImpl extends AbstractDoctrineRepository implements AssociationQueryRepositoryInterface
{

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof AssociationSqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM nmt_inventory_association WHERE 1";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryAssociation', 'nmt_inventory_association');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
