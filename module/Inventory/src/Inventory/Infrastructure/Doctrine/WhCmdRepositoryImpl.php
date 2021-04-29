<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryWarehouse;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\Location\BaseLocationSnapshot;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface;
use Inventory\Infrastructure\Mapper\WhMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WhCmdRepositoryImpl extends AbstractDoctrineRepository implements WhCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtInventoryWarehouse";

    const LOCATION_ENTITY_NAME = "\Application\Entity\NmtInventoryWarehouseLocation";

    public function removeLocation(GenericWarehouse $rootEntity, GenericLocation $localEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeLocation()
     */
    public function storeLocation(GenericWarehouse $rootEntity, GenericLocation $localEntity, $isPosting = false)
    {
        if (! $rootEntity instanceof GenericWarehouse) {
            throw new InvalidArgumentException("Root entity GenericWarehouse not given.");
        }

        /**
         *
         * @var LocationSnapshot $localSnapshot ;
         */
        $localSnapshot = $this->_getLocationSnapshot($localEntity);

        $rootEntityDoctrine = $this->getDoctrineEM()->find(WhCmdRepositoryImpl::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeLocation($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Something wrong. Row Doctrine Entity not created");
        }

        $localSnapshot->id = $rowEntityDoctrine->getId();
        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeWarehouse()
     */
    public function storeWarehouse(GenericWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity
         */
        $entity = $this->_storeWarehouse($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::store()
     */
    public function store(GenericWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        $locations = $rootEntity->getLocations();

        if (count($locations) == null) {
            throw new InvalidArgumentException("Document is empty." . __FUNCTION__);
        }

        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isPosting = true;
        $isFlush = false;
        $increaseVersion = true;

        $rootEntityDoctrine = $this->_storeWarehouse($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Root doctrine entity not found.");
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($locations as $localEntity) {
            $localSnapshot = $this->_getLocationSnapshot($localEntity);
            $n ++;

            $this->_storeLocation($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n);
        }

        // it is time to flush.
        $this->doctrineEM->flush();

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();
        return $rootSnapshot;
    }

    private function _storeWarehouse(WarehouseSnapshot $rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion)
    {
        if ($rootSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not given.");
        }

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity ;
         *
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(WhCmdRepositoryImpl::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $rootClassName = WhCmdRepositoryImpl::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = WhMapper::mapSnapshotEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

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

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $entity;
    }

    private function _storeLocation($rootEntityDoctrine, BaseLocationSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        if (! $rootEntityDoctrine instanceof NmtInventoryWarehouse) {
            throw new InvalidArgumentException("Doctrine root (NmtInventoryWarehouse) not given!");
        }

        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Row snapshot is not given!");
        }

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouseLocation $rowEntityDoctrine ;
         */

        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(WhCmdRepositoryImpl::LOCATION_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            // update
            if ($rowEntityDoctrine->getWarehouse() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }
            // update
            if (! $rowEntityDoctrine->getWarehouse()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getQo()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = WhCmdRepositoryImpl::LOCATION_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            /**
             *
             * @todo: To update.
             */
            $rowEntityDoctrine->setWarehouse($rootEntityDoctrine);
        }

        $rowEntityDoctrine = WhMapper::mapLocationSnapshotEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($increaseVersion) {
            $rootEntityDoctrine->setRevisionNo($rootEntityDoctrine->getRevisionNo() + 1);
            $this->doctrineEM->persist($rootEntityDoctrine);
        }

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $rowEntityDoctrine;
    }

    /**
     *
     * @param GenericWarehouse $rootEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Warehouse\WarehouseSnapshot
     */
    private function _getRootSnapshot(GenericWarehouse $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        /**
         *
         * @todo
         * @var WarehouseSnapshot $rootSnapshot ;
         */
        $rootSnapshot = $rootEntity->makeSnapshot();
        if (! $rootSnapshot instanceof WarehouseSnapshot) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $rootSnapshot;
    }

    /**
     *
     * @param GenericLocation $localEntity
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    private function _getLocationSnapshot(GenericLocation $localEntity)
    {
        if (! $localEntity instanceof GenericLocation) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        /**
         *
         * @todo
         * @var LocationSnapshot $localSnapshot ;
         */
        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }
}