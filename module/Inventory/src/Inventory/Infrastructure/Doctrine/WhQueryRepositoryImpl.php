<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\Factory\WarehouseFactory;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface;
use Inventory\Infrastructure\Mapper\WhMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WhQueryRepositoryImpl extends AbstractDoctrineRepository implements WhQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    public function getVersionArray($id, $token = null)
    {}

    public function getById($id)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        $snapshot = WhMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        return $snapshot;
    }

    public function getLocations($warehouseId)
    {}

    public function getByUUID($uuid)
    {}

    public function getLocationById()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface::getByTokenId()
     */
    public function getByTokenId($id, $token)
    {
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryWarehouse')->findOneBy($criteria);
        $snapshot = WhMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        $wh = WarehouseFactory::contructFromDB($snapshot);

        foreach ($snapshot->getLocationList() as $locationEntity) {
            $locationSnapshot = WhMapper::createLocationSnapshot($this->doctrineEM, $locationEntity);
            $location = GenericLocation::makeFromSnapshot($locationSnapshot);
            $wh->addLocation($location);
        }
        return $wh;
    }
}
