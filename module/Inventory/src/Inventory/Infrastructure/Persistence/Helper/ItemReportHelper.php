<?php
namespace Inventory\Infrastructure\Persistence\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Item\Contracts\ItemType;
use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
use Inventory\Infrastructure\Persistence\Filter\ItemSerialSqlFilter;
use Inventory\Infrastructure\Persistence\SQL\ItemReportSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportHelper
{

    static public function getItem(EntityManager $doctrineEM, $item_id, $item_token)
    {
        // PICTURE
        $join1_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN nmt_inventory_item_picture.is_active =1 THEN (nmt_inventory_item_picture.id) ELSE NULL END) AS total_picture
FROM nmt_inventory_item
LEFT JOIN nmt_inventory_item_picture
ON nmt_inventory_item_picture.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s AND nmt_inventory_item.token='%s'
)
AS nmt_inventory_item_picture
ON nmt_inventory_item.id=nmt_inventory_item_picture.item_id ";
        $join1 = sprintf($join1_tmp, $item_id, $item_token);

        // Attachment
        $join2_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN nmt_application_attachment.is_active =1 THEN (nmt_application_attachment.id) ELSE NULL END) AS total_attachment
FROM nmt_inventory_item
LEFT JOIN nmt_application_attachment
ON nmt_application_attachment.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s AND nmt_inventory_item.token='%s'
)
AS nmt_application_attachment
ON nmt_inventory_item.id=nmt_application_attachment.item_id ";
        $join2 = sprintf($join2_tmp, $item_id, $item_token);

        // PR_ROW
        $join3_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) AS total_pr_row
FROM nmt_inventory_item
LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr_row.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s AND nmt_inventory_item.token='%s'
)
AS nmt_procure_pr_row
ON nmt_inventory_item.id=nmt_procure_pr_row.item_id ";
        $join3 = sprintf($join3_tmp, $item_id, $item_token);

        // AP ROW
        $join4_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.id) ELSE NULL END) AS total_ap_row
FROM nmt_inventory_item
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s AND nmt_inventory_item.token='%s'
)
AS fin_vendor_invoice_row
ON nmt_inventory_item.id=fin_vendor_invoice_row.item_id ";
        $join4 = sprintf($join4_tmp, $item_id, $item_token);

        // PO_ROW
        $join5_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN nmt_procure_po_row.is_active =1 THEN (nmt_procure_po_row.id) ELSE NULL END) AS total_po_row
FROM nmt_inventory_item
LEFT JOIN nmt_procure_po_row
ON nmt_procure_po_row.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s AND nmt_inventory_item.token='%s'
)
AS nmt_procure_po_row
ON nmt_inventory_item.id=nmt_procure_po_row.item_id ";

        $join5 = sprintf($join5_tmp, $item_id, $item_token);

        // PO_ROW
        $join6_tmp = "
JOIN
(
SELECT
	nmt_inventory_item.id AS item_id,
  	COUNT(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.id) ELSE NULL END) AS total_qo_row
FROM nmt_inventory_item
LEFT JOIN nmt_procure_qo_row
ON nmt_procure_qo_row.item_id = nmt_inventory_item.id
WHERE nmt_inventory_item.id=%s
)
AS nmt_procure_qo_row
ON nmt_inventory_item.id=nmt_procure_qo_row.item_id ";

        $join6 = sprintf($join6_tmp, $item_id);

        $sql = "
SELECT
	nmt_inventory_item.*,
	nmt_application_attachment.total_attachment,
	nmt_procure_pr_row.total_pr_row,
	nmt_procure_po_row.total_po_row,
	nmt_procure_qo_row.total_qo_row,
	fin_vendor_invoice_row.total_ap_row,
            
	nmt_inventory_item_picture.total_picture
FROM nmt_inventory_item";

        $sql = $sql . $join1 . $join2 . $join3 . $join4 . $join5 . $join6;
        // echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_pr_row", "total_pr_row");
            $rsm->addScalarResult("total_picture", "total_picture");
            $rsm->addScalarResult("total_attachment", "total_attachment");
            $rsm->addScalarResult("total_po_row", "total_po_row");
            $rsm->addScalarResult("total_ap_row", "total_ap_row");
            $rsm->addScalarResult("total_qo_row", "total_qo_row");

            $query = $doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    static public function getList(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        $sql = "SELECT * FROM nmt_inventory_item";

        // when paginator needed
        if ($limit > 0) {
            $sql1 = "SELECT id from nmt_inventory_item";

            $sql1 = $sql1 . " WHERE 1";

            if ($filter == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
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

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getItemListWithSerialNumber(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof ItemSerialSqlFilter) {
            return null;
        }

        $sql = ItemReportSQL::ITEM_LIST_WITH_SN;

        // $sql = $sql . ' AND nmt_inventory_item.is_active=1';

        if ($filter->getItemId() > 0) {
            $format = ' AND nmt_inventory_item.id=%s';
            $sql = $sql . \sprintf($format, $filter->getItemId());
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param SqlFilterInterface $filter
     * @param string $sort_by
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    static public function getItemList(EntityManager $doctrineEM, SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        if (! $filter instanceof ItemReportSqlFilter) {
            return null;
        }

        $sql = "SELECT * FROM nmt_inventory_item WHERE 1";

        if ($filter->getIsActive() == 1) {
            $format = ' AND nmt_inventory_item.is_active= 1';
            $sql = $sql . \sprintf($format, $filter->getIsActive());
        } elseif ($filter->getIsActive() == 0) {
            $format = ' AND nmt_inventory_item.is_active= 0';
            $sql = $sql . \sprintf($format, $filter->getIsActive());
        }

        $format = ' AND nmt_inventory_item.item_type_id= %s';

        switch ($filter->getItemType()) {
            case ItemType::FIXED_ASSET_ITEM_TYPE:
                $sql = $sql . \sprintf($format, ItemType::FIXED_ASSET_ITEM_TYPE);
                break;

            case ItemType::NONE_INVENTORY_ITEM_TYPE:
                $sql = $sql . \sprintf($format, ItemType::NONE_INVENTORY_ITEM_TYPE);
                break;

            case ItemType::INVENTORY_ITEM_TYPE:
                $sql = $sql . \sprintf($format, ItemType::INVENTORY_ITEM_TYPE);
                break;

            case ItemType::SERVICE_ITEM_TYPE:
                $sql = $sql . \sprintf($format, ItemType::SERVICE_ITEM_TYPE);
                break;
        }

        switch ($sort_by) {
            case "itemName":
                $sql = $sql . " ORDER BY nmt_inventory_item.item_name " . $sort;
                break;
            case "createdOn":
                $sql = $sql . " ORDER BY nmt_inventory_item.created_on " . $sort;
                break;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";
        echo $sql;
        try {
            $rsm = new ResultSetMappingBuilder($doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $query = $doctrineEM->createNativeQuery($sql, $rsm);

            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
