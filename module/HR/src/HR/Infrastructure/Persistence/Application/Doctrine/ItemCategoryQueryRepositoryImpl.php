<?php
namespace Inventory\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Infrastructure\Persistence\ItemCategoryQueryRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCategoryQueryRepositoryImpl extends AbstractDoctrineRepository implements ItemCategoryQueryRepositoryInterface
{

    public function getCategory($catId)
    {
        $sql = "select
*
from nmt_inventory_item_category
where node_id=" . $catId;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemCategory', 'nm_inventory_item_category');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getCategoryByName($catName)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemCategoryQueryRepositoryInterface::getRoot()
     */
    public function getRoot($rootId)
    {
        $sql = "select
*
from nmt_inventory_item_category
where root_id=" . $rootId;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemCategory', 'nm_inventory_item_category');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $resulst = $query->getResult();
            return $resulst;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
