<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryWarehouse;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Application\Entity\NmtInventoryWarehouseLocation;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineWarehouseQueryRepository extends AbstractDoctrineRepository implements WarehouseQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        if ($id == null)
            return null;

        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtInventoryWarehouse")->findOneBy($criteria);

        /**
         *
         * @var TransactionSnapshot $snapshot ;
         */
        $snapshot = $this->createSnapshot($entity);
        if ($snapshot == null)
            return null;

        $wh = new GenericWarehouse();
        $wh->makeFromSnapshot($snapshot);

        $criteria = array(
            'warehouse' => $entity
        );
        $sort = array();
        $locations = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouseLocation')->findBy($criteria, $sort);

        foreach ($locations as $r) {

            /** @var \Application\Entity\NmtInventoryWarehouseLocation $r */
            $locationSnapshot = $this->createLocationSnapshot($r);

            if ($locationSnapshot == null)
                continue;

            $location = new GenericLocation();
            $location->makeFromSnapshot($locationSnapshot);
            $wh->addLocation($location);

            if ($location->getIsRootLocation() == 1) {
                $wh->setRootLocation($location);
            }

            if ($location->getIsReturnLocation() == 1) {
                $wh->setReturnLocation($location);
            }

            if ($location->getIsScrapLocation() == 1) {
                $wh->setScrapLocation($location);
            }
        }

        return $wh;
    }

    public function getLocationOf()
    {}

    public function getByUUID($uuid)
    {}

    public function getLocationById()
    {}

    public function findAll()
    {}

    /**
     *
     * @param \Application\Entity\NmtInventoryWarehouse $entity
     *
     */
    private function createSnapshot(NmtInventoryWarehouse $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new WarehouseSnapshot();

        // mapping referrence

        $snapshot->id = $entity->getId();
        $snapshot->whCode = $entity->getWhCode();
        $snapshot->whName = $entity->getWhName();
        $snapshot->whAddress = $entity->getWhAddress();
        $snapshot->whContactPerson = $entity->getWhContactPerson();
        $snapshot->whTelephone = $entity->getWhTelephone();
        $snapshot->whEmail = $entity->getWhEmail();
        $snapshot->isLocked = $entity->getIsLocked();
        $snapshot->whStatus = $entity->getWhStatus();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isDefault = $entity->getIsDefault();

        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->token = $entity->getToken();

        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->uuid = $entity->getUuid();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if (! $entity->getLastChangeOn() == null) {
            $snapshot->lastChangeOn = $entity->getLastChangeOn()->format("Y-m-d");
        }

        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getCompany() !== null) {
            $snapshot->company = $entity->getCompany()->getId();
        }

        if ($entity->getWhCountry() !== null) {
            $snapshot->whCountry = $entity->getWhCountry()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getStockkeeper() !== null) {
            $snapshot->stockkeeper = $entity->getStockkeeper()->getId();
        }

        if ($entity->getWhController() !== null) {
            $snapshot->whController = $entity->getWhController()->getId();
        }

        if ($entity->getLocation() !== null) {
            $snapshot->location = $entity->getLocation()->getId();
        }

        return $snapshot;
    }

    /**
     *
     * @param NmtInventoryWarehouseLocation $entity
     * @return NULL|\Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    private function createLocationSnapshot(NmtInventoryWarehouseLocation $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new LocationSnapshot();

        $snapshot->id = $entity->getId();

        $snapshot->sysNumber = $entity->getSysNumber();
        $snapshot->token = $entity->getToken();
        $snapshot->revisionNo = $entity->getRevisionNo();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->isSystemLocation = $entity->getIsSystemLocation();
        $snapshot->isReturnLocation = $entity->getIsReturnLocation();
        $snapshot->isScrapLocation = $entity->getIsScrapLocation();
        $snapshot->isRootLocation = $entity->getIsRootLocation();
        $snapshot->locationName = $entity->getLocationName();
        $snapshot->locationCode = $entity->getLocationCode();
        $snapshot->parentId = $entity->getParentId();
        $snapshot->locationType = $entity->getLocationType();
        $snapshot->isActive = $entity->getIsActive();
        $snapshot->isLocked = $entity->getIsLocked();
        $snapshot->path = $entity->getPath();
        $snapshot->pathDepth = $entity->getPathDepth();
        $snapshot->hasMember = $entity->getHasMember();
        $snapshot->uuid = $entity->getUuid();

        // $snapshot->createdBy = $entity->getCreatedBy();
        // $snapshot->lastChangeBy = $entity->getLastChangeBy();

        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d");
        }

        if ($entity->getWarehouse() !== null) {
            $snapshot->warehouse = $entity->getWarehouse()->getId();
        }

           
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }
        
        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }
        
        return $snapshot;
    }
}
