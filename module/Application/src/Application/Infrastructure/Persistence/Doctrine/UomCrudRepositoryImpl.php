<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Infrastructure\Mapper\UomMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
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
class UomCrudRepositoryImpl extends AbstractDoctrineRepository implements CrudRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationUom";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface::getByKey()
     */
    public function getByKey($key)
    {
        $criteria = array(
            'uomName' => $key
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationUom $doctrineEntity ;
         */

        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationUom')->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }

        $snapshot = UomMapper::createSnapshot($this->getDoctrineEM(), $entity, new UomSnapshot());
        return Uom::createFrom($snapshot);
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

        $sql = "SELECT * FROM nmt_application_uom WHERE 1";

        if ($filter->getSortBy() == null) {
            $filter->setSortBy('uomName');
        }

        if ($filter->getSort() == null) {
            $filter->setSort(SqlKeyWords::ASC);
        }

        switch ($filter->getSortBy()) {
            case "uomName":
                $sql = $sql . " ORDER BY nmt_application_uom.uom_name " . $filter->getSort();
                break;

            case "uomCode":
                $sql = $sql . " ORDER BY nmt_application_uom.uom_code " . $filter->getSort();
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_application_uom.created_on " . $filter->getSort();
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
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationUom', 'nmt_application_uom');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $results = $query->getResult();

            if ($results == null) {
                return $uoms;
            }

            foreach ($results as $result) {
                $snapshot = UomMapper::createSnapshot($this->getDoctrineEM(), $result, new UomSnapshot());
                $uoms->add(Uom::createFrom($snapshot));
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
        if (! $snapshot instanceof UomSnapshot) {
            throw new InvalidArgumentException("UomSnapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtApplicationUom $entity ;
         *
         */
        if ($snapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $snapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $snapshot->getId()));
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = UomMapper::mapSnapshotEntity($this->getDoctrineEM(), $snapshot, $entity);

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        return $entity;
    }

    public function update($valueObject)
    {}

    public function delete($valueObject)
    {}
}
