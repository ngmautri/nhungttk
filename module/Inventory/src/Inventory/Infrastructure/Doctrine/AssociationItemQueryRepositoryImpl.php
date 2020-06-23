<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\Repository\AssociationItemQueryRepositoryInterface;
use Inventory\Infrastructure\Mapper\AssociationMapper;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\AssociationSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationItemQueryRepositoryImpl extends AbstractDoctrineRepository implements AssociationItemQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Association\Repository\AssociationItemQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryAssociationItem $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryAssociationItem')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    public function getVersionArray($id, $token = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Association\Repository\AssociationItemQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'uuid' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryAssociationItem')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = AssociationMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = BaseAssociation::contructFromDB($rootSnapshot);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Association\Repository\AssociationItemQueryRepositoryInterface::getList()
     */
    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof AssociationSqlFilter) {
            return null;
        }

        $sql = "SELECT nmt_inventory_association_item. * FROM nmt_inventory_association_item WHERE 1";

        if ($filter->getItemId() > 0) {

            $format = ' AND main_item_id = %s';
            $sql = $sql . \sprintf($format, $filter->getItemId());

            $sql = $sql . ' UNION ';

            $sql = $sql . "SELECT nmt_inventory_association_item.* FROM nmt_inventory_association_item WHERE 1";
            $format = ' AND related_item_id = %s and has_both_direction=1';
            $sql = $sql . \sprintf($format, $filter->getItemId());
        }

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryAssociationItem', 'nmt_inventory_association_item');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
