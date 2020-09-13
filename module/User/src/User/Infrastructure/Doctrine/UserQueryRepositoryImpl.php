<?php
namespace User\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Warehouse\Factory\WarehouseFactory;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Infrastructure\Mapper\WhMapper;
use User\Domain\User\Factory\UserFactory;
use User\Domain\User\Repository\UserQueryRepositoryInterface;
use User\Infrastructure\Mapper\UserMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserQueryRepositoryImpl extends AbstractDoctrineRepository implements UserQueryRepositoryInterface
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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Repository\WhQueryRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\MlaUsers $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\MlaUsers')->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }
        $snapshot = UserMapper::createSnapshot($this->doctrineEM, $entity);

        if ($snapshot == null) {
            return null;
        }

        $user = UserFactory::contructFromDB($snapshot);
        return $user;
    }

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
