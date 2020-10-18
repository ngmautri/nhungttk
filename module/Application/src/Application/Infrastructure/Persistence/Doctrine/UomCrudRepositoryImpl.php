<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Infrastructure\Mapper\UomMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface;
use Application\Infrastructure\Persistence\Contracts\SqlFilterInterface;
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

    public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $filter instanceof SqlFilterInterface) {
            return null;
        }

        $sql = "SELECT * FROM nmt_application_uom WHERE 1";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationUom', 'nmt_application_uom');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\CrudRepositoryInterface::save()
     */
    public function save($snapshot)
    {
        if ($snapshot instanceof UomSnapshot) {
            throw new InvalidArgumentException("Root snapshot not given.");
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

        return $entity;
    }

    public function update($valueObject)
    {}

    public function delete($valueObject)
    {}
}
