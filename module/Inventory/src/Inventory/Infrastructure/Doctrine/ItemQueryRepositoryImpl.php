<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface;
use Inventory\Infrastructure\Doctrine\Helper\ItemCollectionHelper;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Webmozart\Assert\Assert;

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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Repository\ItemQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $doctrineEM = $this->getDoctrineEM();
        $rootEntityDoctrine = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);
        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemMapper::createSnapshot($rootEntityDoctrine, null, true);
        $rootEntity = ItemFactory::contructFromDB($rootSnapshot);

        Assert::notNull($rootEntity);
        $rootEntity->setVariantCollectionRef(ItemCollectionHelper::createVariantCollectionRef($doctrineEM, $id));

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

        $doctrineEM = $this->getDoctrineEM();
        $rootEntityDoctrine = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemMapper::createSnapshot($rootEntityDoctrine, null, true);
        $rootEntity = ItemFactory::contructFromDB($rootSnapshot);

        Assert::notNull($rootEntity);
        $rootEntity->setVariantCollectionRef(ItemCollectionHelper::createVariantCollectionRef($doctrineEM, $id));

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
        $doctrineEM = $this->getDoctrineEM();
        $doctrineEntity = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->findOneBy($criteria);

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

    /*
     * |=============================
     * | For Lazy Loading
     * |
     * |=============================
     */
}
