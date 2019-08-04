<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Entity\NmtInventoryWarehouse;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\GenericWarehouse;

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
             echo $r->getLocationCode() ."\n";   
            
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
}
