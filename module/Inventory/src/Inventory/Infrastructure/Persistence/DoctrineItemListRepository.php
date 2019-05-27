<?php
namespace Inventory\Infrastructure\Persistence;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemListRepository extends AbstractDoctrineRepository implements ItemListRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemListRepositoryInterface::getTotalItem()
     */
    public function getTotalItem($item_type = null, $is_active = null, $is_fixed_asset = null)
    {
        $sql = "SELECT count(*) as total_row FROM nmt_inventory_item Where 1 ";

        if ($item_type == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
            $sql = $sql . sprintf(" AND nmt_inventory_item.item_type ='%s'", $item_type);
        }

        if ($is_active == 1) {
            $sql = $sql . " AND nmt_inventory_item.is_active = 1";
        } elseif ($is_active == - 1) {
            $sql = $sql . " AND nmt_inventory_item.is_active = 0";
        }

        if ($is_fixed_asset == 1) {
            $sql = $sql . " AND nmt_inventory_item.is_fixed_asset = 1";
        } elseif ($is_fixed_asset == - 1) {
            $sql = $sql . " AND nmt_inventory_item.is_fixed_asset = 0";
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_row", "total_row");
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return (int) $result['total_row'];
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemListRepositoryInterface::getItems()
     */
    public function getItems($item_type = null, $is_active = null, $is_fixed_asset = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM nmt_inventory_item";

        // when paginator needed
        if ($limit > 0) {
            $sql1 = "SELECT id from nmt_inventory_item";

            $sql1 = $sql1 . " WHERE 1";

            if ($item_type == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
                $sql1 = $sql1 . sprintf(" AND nmt_inventory_item.item_type ='%s'", $item_type);
            }

            if ($is_active == 1) {
                $sql1 = $sql1 . " AND nmt_inventory_item.is_active = 1";
            } elseif ($is_active == - 1) {
                $sql1 = $sql1 . " AND nmt_inventory_item.is_active = 0";
            }

            if ($is_fixed_asset == 1) {
                $sql1 = $sql1 . " AND nmt_inventory_item.is_fixed_asset = 1";
            } elseif ($is_fixed_asset == - 1) {
                $sql1 = $sql1 . " AND nmt_inventory_item.is_fixed_asset = 0";
            }

            if ($sort_by == "itemName") {
                $sql1 = $sql1 . " ORDER BY nmt_inventory_item.item_name " . $sort;
            } elseif ($sort_by == "createdOn") {
                $sql1 = $sql1 . " ORDER BY nmt_inventory_item.created_on " . $sort;
            }

            $sql1 = $sql1 . " LIMIT " . $limit;

            $sql1 = $sql1 . " OFFSET " . $offset;

            $sql = $sql . " INNER JOIN (" . $sql1 . ") as t1 on t1.id = nmt_inventory_item.id";
        } else {

            $sql = $sql . " WHERE 1";

            if ($item_type == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
                $sql = $sql . sprintf(" AND nmt_inventory_item.item_type ='%s'", $item_type);
            }

            if ($is_active == 1) {
                $sql = $sql . " AND nmt_inventory_item.is_active = 1";
            } elseif ($is_active == - 1) {
                $sql = $sql . " AND nmt_inventory_item.is_active = 0";
            }

            if ($is_fixed_asset == 1) {
                $sql = $sql . " AND nmt_inventory_item.is_fixed_asset = 1";
            } elseif ($is_fixed_asset == - 1) {
                $sql = $sql . " AND nmt_inventory_item.is_fixed_asset = 0";
            }

            if ($sort_by == "itemName") {
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
            } elseif ($sort_by == "createdOn") {
                $sql = $sql . " ORDER BY nmt_inventory_item.created_on " . $sort;
            }
        }
        $stmt = $this->doctrineEM->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
