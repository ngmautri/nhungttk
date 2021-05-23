<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface;
use Inventory\Infrastructure\Mapper\ItemMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemQueryRepositoryImpl extends AbstractDoctrineRepository implements ItemQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getDocVersion()
            ];
        }

        return null;
    }

    public function getRootEntityByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItem')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = ItemFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getRootEntityById()
     */
    public function getRootEntityById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItem')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = ItemFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getItemTypeById()
     */
    public function getItemTypeById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItem $doctrineEntity ;
         */
        $doctrineEntity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItem')
            ->findOneBy($criteria);

        return $doctrineEntity->getItemTypeId();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getItemSnapshotById()
     */
    public function getItemSnapshotById($id, $needDetails = false)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItem')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        return ItemMapper::createSnapshot($rootEntityDoctrine, null, $needDetails);
    }
}
