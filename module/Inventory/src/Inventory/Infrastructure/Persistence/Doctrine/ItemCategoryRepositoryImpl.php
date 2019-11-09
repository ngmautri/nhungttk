<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\NoResultException;
use Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface;
use Inventory\Infrastructure\Mapper\ItemMapper;
use Inventory\Domain\Item\ItemSnapshot;

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
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryRepositoryInterface::getItemsByCategory()
     */
    public function getItemsByCategory($catId)
    {
        $results = $this->_getItemsByCategory($catId);

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

    private function _getItemsByCategory($catId)
    {
        $sql = "select
nmt_inventory_item.*,
nmt_inventory_item_category_member.category_id
from
nmt_inventory_item_category_member
left join nmt_inventory_item
on nmt_inventory_item.id = nmt_inventory_item_category_member.item_id where 1=1 AND nmt_inventory_item_category_member.category_id=" . $catId;

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

    public function getTotalItemsByCategory($catId)
    {
        return $this->_getTotalItemsByCategory($catId);
        
    }

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
        } catch (NoResultException $e) {
            
        }
        return 0;
    }
}
