<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Association\BaseAssociation;
use Inventory\Domain\Association\BaseAssociationSnapshot;
use Inventory\Domain\Association\Repository\AssociationCmdRepositoryInterface;
use Inventory\Infrastructure\Mapper\AssociationMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationCmdRepositoryImpl extends AbstractDoctrineRepository implements AssociationCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryAssociationItem";

    public function store(BaseAssociation $rootEntity, $generateSysNumber = True)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseAssociation not retrieved.");
        }

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $increaseVersion = false;

        /**
         *
         * @var \Application\Entity\NmtInventoryAssociationItem $entity ;
         *     
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
            $increaseVersion = true;
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = AssociationMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        /*
         * if ($generateSysNumber) {
         * $entity->setSysNumber($this->generateSysNumber($entity));
         * }
         */

        if ($increaseVersion) {
            // Optimistic Locking
            if ($rootSnapshot->getId() > 0) {
                $entity->setRevisionNo($entity->getRevisionNo() + 1);
            }
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();

        $rootSnapshot->id = $entity->getId();
        return $rootSnapshot;
    }

    /**
     *
     * @param BaseAssociation $rootEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Association\BaseAssociation
     */
    private function _getRootSnapshot(BaseAssociation $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @var BaseAssociation $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof BaseAssociationSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created! AssociationCmdRepositoryImpl");
        }

        return $rootSnapshot;
    }
}