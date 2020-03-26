<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\NoResultException;
use Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Inventory\Domain\Item\ItemSnapshot;
use Application\Entity\NmtInventoryItemCategoryMember;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryRepositoryImpl extends AbstractDoctrineRepository implements ItemCategoryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface::getNoneCategorizedItems()
     */
    public function getNoneCategorizedItems($limit, $offset)
    {
        $results = $this->_getNoneCategorizedItems($limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtInventoryItem $entity ;*/
            $entity = $r[0];

            $snapShot = ItemMapper::createSnapshot($entity, new ItemSnapshot());

            if ($snapShot == null) {
                continue;
            }

            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    /**
     *
     * @param int $catId
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function _getNoneCategorizedItems($limit, $offset)
    {
        $sql = "select
nmt_inventory_item.*
from nmt_inventory_item
left join nmt_inventory_item_category_member
on nmt_inventory_item.id = nmt_inventory_item_category_member.item_id
where isnull(nmt_inventory_item_category_member.category_id)";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nm_inventory_item');
            $rsm->addScalarResult("category_id", "null");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getNoneCategorizedItemsTotal()
    {
        return $this->_getNoneCategorizedItemsTotal();
    }

    private function _getNoneCategorizedItemsTotal()
    {
        $sql = "select
count(nmt_inventory_item.id) as total_rows
from nmt_inventory_item
left join nmt_inventory_item_category_member
on nmt_inventory_item.id = nmt_inventory_item_category_member.item_id
where isnull(nmt_inventory_item_category_member.category_id)";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addScalarResult("total_rows", "total_rows");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            if (count($result) == 1) {
                return (int) $result[0]['total_rows'];
            }
        } catch (NoResultException $e) {}
        return 0;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface::addItemToCategory()
     */
    public function addItemToCategory($itemId, $catId, $userId)
    {
        $criteria = array(
            'item' => $itemId,
            'category' => $catId
        );

        /**
         *
         * @var \Application\Entity\NmtInventoryItemCategoryMember $entity ;
         */
        $entity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItemCategoryMember')
            ->findOneBy($criteria);

        if (! $entity == null) {
            throw new \Exception("Exits..");
        }

        $criteria = array(
            'item' => $itemId
        );

        $entity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\NmtInventoryItemCategoryMember')
            ->findOneBy($criteria);

        if ($entity == null) {
            $entity = new NmtInventoryItemCategoryMember();
            $obj = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItemCategory')->find($catId);
            $entity->setCategory($obj);

            $obj = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItem')->find($itemId);
            $entity->setItem($obj);

            $obj = $this->doctrineEM->getRepository('\Application\Entity\MlaUsers')->find($userId);
            $entity->setCreatedBy($obj);
            $entity->setCreatedOn(new \DateTime());
        } else {
            $obj = $this->doctrineEM->getRepository('\Application\Entity\NmtInventoryItemCategory')->find($catId);
            $entity->setCategory($obj);
        }

        $this->getDoctrineEM()->persist($entity);
        $this->getDoctrineEM()->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface::getItemsByCategory()
     */
    public function getItemsByCategory($catId, $limit, $offset)
    {
        $results = $this->_getItemsByCategory($catId, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\NmtInventoryItem $entity ;*/
            $entity = $r[0];

            $snapShot = ItemMapper::createSnapshot($entity, new ItemSnapshot());

            if ($snapShot == null) {
                continue;
            }

            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    /**
     *
     * @param int $catId
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    private function _getItemsByCategory($catId, $limit, $offset)
    {
        $sql = "select
nmt_inventory_item.*,
nmt_inventory_item_category_member.category_id
from
nmt_inventory_item_category_member
left join nmt_inventory_item
on nmt_inventory_item.id = nmt_inventory_item_category_member.item_id where 1=1 AND nmt_inventory_item_category_member.category_id=" . $catId;

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nm_inventory_item');
            $rsm->addScalarResult("category_id", "category_id");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface::getTotalItemsByCategory()
     */
    public function getTotalItemsByCategory($catId)
    {
        return $this->_getTotalItemsByCategory($catId);
    }

    /**
     *
     * @param int $catId
     * @return number
     */
    private function _getTotalItemsByCategory($catId)
    {
        $sql = "select
count(nmt_inventory_item.id) as total_rows
from
nmt_inventory_item_category_member
left join nmt_inventory_item
on nmt_inventory_item.id = nmt_inventory_item_category_member.item_id where 1=1 AND nmt_inventory_item_category_member.category_id=" . $catId;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addScalarResult("total_rows", "total_rows");
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            if (count($result) == 1) {
                return (int) $result[0]['total_rows'];
            }
        } catch (NoResultException $e) {}
        return 0;
    }
}
