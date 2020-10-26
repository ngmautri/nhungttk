<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Domain\Shared\Uom\UomGroup;
use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Infrastructure\Mapper\UomGroupMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\CompositeCrudRepositoryInterface;
use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomGroupCrudRepositoryImpl extends AbstractDoctrineRepository implements CompositeCrudRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\AppUomGroup";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface::getByKey()
     */
    public function getByKey($key)
    {
        $criteria = array(
            'groupName' => $key
        );

        /**
         *
         * @var \Application\Entity\AppUomGroup $doctrineEntity ;
         */

        $entity = $this->doctrineEM->getRepository('\Application\Entity\AppUomGroup')->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }

        $snapshot = UomGroupMapper::createSnapshot($this->getDoctrineEM(), $entity, new UomGroupSnapshot());
        return UomGroup::createFrom($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface::getList()
     */
    public function getList(DefaultListSqlFilter $filter)
    {
        if (! $filter instanceof DefaultListSqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM app_uom_group WHERE 1";

        if ($filter->getSortBy() == null) {
            $filter->setSortBy('groupName');
        }

        if ($filter->getSort() == null) {
            $filter->setSort(SqlKeyWords::ASC);
        }

        switch ($filter->getSortBy()) {
            case "groupName":
                $sql = $sql . " ORDER BY app_uom_group.group_name " . $filter->getSort();
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY app_uom_groups.created_on " . $filter->getSort();
                break;
        }

        if ($filter->getOffset() > 0) {
            $sql = $sql . ' OFFSET ' . $filter->getOffset();
        }

        if ($filter->getLimit() > 0) {
            $sql = $sql . ' LIMIT ' . $filter->getLimit();
        }
        $sql = $sql . ";";

        $uoms = new ArrayCollection();

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata(self::ROOT_ENTITY_NAME, 'app_uom_group');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $results = $query->getResult();

            if ($results == null) {
                return $uoms;
            }

            foreach ($results as $result) {
                $snapshot = UomGroupMapper::createSnapshot($this->getDoctrineEM(), $result, new UomGroupSnapshot());
                $uoms->add(UomGroup::createFrom($snapshot));
            }
        } catch (NoResultException $e) {
            // left blank
        }

        return $uoms;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface::save()
     */
    public function save($snapshot)
    {
        if (! $snapshot instanceof UomGroupSnapshot) {
            throw new InvalidArgumentException("UomGroupSnapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\AppUomGroup $entity ;
         *
         */
        if ($snapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $snapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $snapshot->getId()));
            }
        } else {
            $rootClassName = UomGroupCrudRepositoryImpl::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = UomGroupMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity;
    }

    public function update($valueObject)
    {}

    public function delete($valueObject)
    {}

    public function saveMember($rootObject, $localObject)
    {}

    public function saveAll($valueObject)
    {}
}
