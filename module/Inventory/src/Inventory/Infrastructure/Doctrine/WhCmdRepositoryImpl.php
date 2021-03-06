<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Entity\NmtInventoryWarehouse;
use Application\Entity\NmtInventoryWarehouseLocation;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\Location\BaseLocation;
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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeWholeWarehouse()
     */
    public function storeWholeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isPosting = true;
        $isFlush = true;
        $increaseVersion = true;
        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity
         */
        $rootEntityDoctrine = $this->_storeWarehouse($rootSnapshot, $generateSysNumber, $isPosting, $isFlush, $increaseVersion);

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $rootEntityDoctrine->getId();
        $rootSnapshot->sysNumber = $rootEntityDoctrine->getSysNumber();
        $rootSnapshot->revisionNo = $rootEntityDoctrine->getRevisionNo();

        $collection = $rootEntity->getLocationCollection();
        if ($collection->isEmpty()) {
            return $rootSnapshot;
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($collection as $localEntity) {
            $n ++;

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $localSnapshot = $this->_getLocationSnapshot($localEntity);
            $this->_storeLocation($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        // it is time to flush.
        $this->doctrineEM->flush();

        return $rootSnapshot;
    }

    /**
     *
     * @de  precated
     * @param BaseWarehouse $rootEntity
     * @param boolean $generateSysNumber
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Warehouse\WarehouseSnapshot
     */
    public function store(BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
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

    /**
     * store only WH
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeWarehouse()
     */
    public function storeWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
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

    public function RemoveWarehouse(BaseCompany $companyEntity, BaseWarehouse $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::removeLocation()
     */
    public function removeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnWarehouse($rootEntity);

        $localSnapshot = $this->_getLocationSnapshot($localEntity);
        $rowEntityDoctrine = $this->assertAndReturnLocation($rootEntityDoctrine, $localSnapshot);

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhCmdRepositoryInterface::storeLocation()
     */
    public function storeLocation(BaseWarehouse $rootEntity, BaseLocation $localEntity, $isPosting = false)
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

    // ===============================
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
            $rootClassName = self::ROOT_ENTITY_NAME;
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

    /**
     *
     * @param NmtInventoryWarehouse $rootEntityDoctrine
     * @param BaseLocationSnapshot $localSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @param int $n
     * @return NULL|\Application\Entity\NmtInventoryWarehouseLocation
     */
    private function _storeLocation(NmtInventoryWarehouse $rootEntityDoctrine, BaseLocationSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {
        $rowEntityDoctrine = $this->assertAndReturnLocation($rootEntityDoctrine, $localSnapshot);
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
    private function _getLocationSnapshot(BaseLocation $localEntity)
    {
        if (! $localEntity instanceof BaseLocation) {
            throw new InvalidArgumentException("Local entity not given!");
        }

        $localSnapshot = $localEntity->makeSnapshot();
        if ($localSnapshot == null) {
            throw new InvalidArgumentException("Root snapshot not created!");
        }

        return $localSnapshot;
    }

    private function assertAndReturnWarehouse(BaseWarehouse $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseWarehouse not given.");
        }

        /**
         *
         * @var NmtInventoryWarehouse $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtInventoryWarehouse) {
            throw new InvalidArgumentException("Warehouse entity not found!");
        }

        return $rootEntityDoctrine;
    }

    /**
     *
     * @param NmtInventoryWarehouse $rootEntityDoctrine
     * @param BaseLocationSnapshot $localSnapshot
     * @throws InvalidArgumentException
     * @return \Application\Entity\NmtInventoryWarehouseLocation
     */
    private function assertAndReturnLocation(NmtInventoryWarehouse $rootEntityDoctrine, BaseLocationSnapshot $localSnapshot)
    {
        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("NmtInventoryWarehouse not found! #%s", ""));
        }
        if ($localSnapshot == null) {
            throw new InvalidArgumentException(sprintf("LocationSnapshot snapshot not found! #%s", ""));
        }

        $rowEntityDoctrine = null;

        if ($localSnapshot->getId() > 0) {

            /**
             *
             * @var NmtInventoryWarehouseLocation $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCATION_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Location entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getWarehouse() == null) {
                throw new InvalidArgumentException("Location entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getWarehouse()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Location entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCATION_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setWarehouse($rootEntityDoctrine);
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create Location  entity!");
        }

        return $rowEntityDoctrine;
    }
}