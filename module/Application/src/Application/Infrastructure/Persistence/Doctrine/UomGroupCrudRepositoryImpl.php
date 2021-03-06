<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Shared\Uom\UomGroup;
use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Domain\Shared\Uom\UomPair;
use Application\Domain\Shared\Uom\UomPairSnapshot;
use Application\Infrastructure\Mapper\UomGroupMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\CompositeCrudRepositoryInterface;
use Application\Infrastructure\Persistence\Contracts\SqlKeyWords;
use Application\Infrastructure\Persistence\Filter\DefaultListSqlFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomGroupCrudRepositoryImpl extends AbstractDoctrineRepository implements CompositeCrudRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\AppUomGroup";

    const LOCAL_ENTITY_NAME = "\Application\Entity\AppUomGroupMember";

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

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\AppUomGroup')->findOneBy($criteria);
        if ($doctrineEntity == null) {
            return null;
        }

        $snapshot = UomGroupMapper::createSnapshot($this->getDoctrineEM(), $doctrineEntity, new UomGroupSnapshot());

        $rootObject = UomGroup::createFrom($snapshot);
        $rows = $this->getMemberById($doctrineEntity->getId());

        if (count($rows) == 0) {
            return $rootObject;
        }

        foreach ($rows as $r) {
            /**@var \Application\Entity\AppUomGroupMember $localEnityDoctrine ;*/
            $localEnityDoctrine = $r;
            $localSnapshot = UomGroupMapper::createUomPairSnapshot($this->getDoctrineEM(), $localEnityDoctrine, new UomPairSnapshot());
            $localSnapshot->baseUom = $rootObject->getBaseUom();
            $localObject = UomPair::createFrom($localSnapshot);
            $rootObject->getMembers()->add($localObject);
        }

        return $rootObject;
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

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CompositeCrudRepositoryInterface::saveMember()
     */
    public function saveMember(ValueObject $rootObject, ValueObject $localObject)
    {
        Assert::isInstanceOf($rootObject, UomGroup::class, 'UomGroup entity not found!');
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootObject->getId());
        Assert::notNull($rootEntityDoctrine, 'Doctrine UomGroup entity not found!');

        $localSnapshot = $this->_getLocalSnapshot($localObject);

        $isFlush = true;
        $rowEntityDoctrine = $this->_storeMember($rootEntityDoctrine, $localSnapshot, $isFlush);

        Assert::notNull($rowEntityDoctrine, 'Something wrong. Row Doctrine Entity not created');

        $localSnapshot->id = $rowEntityDoctrine->getId();
        return $localSnapshot;
    }

    public function saveAll($valueObject)
    {}

    private function getMemberById($id)
    {
        $sql = "
select
*
from app_uom_group_member
where app_uom_group_member.group_id=%s";

        $sql = sprintf($sql, $id);

        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\AppUomGroupMember', 'app_uom_group_member');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _storeMember($rootEntityDoctrine, UomPairSnapshot $localSnapshot, $isFlush)
    {

        /**
         *
         * @var \Application\Entity\AppUomGroupMember $rowEntityDoctrine ;
         */
        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());
            Assert::notNull($rowEntityDoctrine, sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();
            $rowEntityDoctrine->setGroup($rootEntityDoctrine);
        }

        $rowEntityDoctrine = UomGroupMapper::mapUomPairSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);
        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $rowEntityDoctrine;
    }

    /**
     *
     * @param UomPair $localObject
     * @return UomPairSnapshot
     */
    private function _getLocalSnapshot(UomPair $localObject)
    {
        Assert::notNull($localObject);
        $localSnapshot = $localObject->makeSnapshot();
        Assert::notNull($localSnapshot);

        return $localSnapshot;
    }
}
