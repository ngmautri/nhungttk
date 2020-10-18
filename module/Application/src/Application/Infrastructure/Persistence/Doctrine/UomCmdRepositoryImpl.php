<?php
namespace Application\Infrastructure\Persistence\Doctrine;

use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Infrastructure\Mapper\UomMapper;
use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Contracts\UomCmdRepositoryInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomCmdRepositoryImpl extends AbstractDoctrineRepository implements UomCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationUom";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\Contracts\UomCmdRepositoryInterface::store()
     */
    public function store(UomSnapshot $snapshot)
    {
        if ($snapshot == null) {
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
}
