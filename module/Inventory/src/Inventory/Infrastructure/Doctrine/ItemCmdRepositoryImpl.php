<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Infrastructure\Mapper\ItemMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCmdRepositoryImpl extends AbstractDoctrineRepository implements ItemCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryItem";

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface::store()
     */
    public function store(GenericItem $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("GenericItem not retrieved.");
        }

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $increaseVersion = false;

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         *     
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }

            $increaseVersion = true;
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ItemMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        if ($generateSysNumber) {
            $entity->setSysNumber($this->generateSysNumber($entity));
        }

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootSnapshot->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->sysNumber = $entity->getSysNumber();
        return $rootSnapshot;
    }

    /**
     *
     * @param GenericItem $rootEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    private function _getRootSnapshot(GenericItem $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @var ItemSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof ItemSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }
}