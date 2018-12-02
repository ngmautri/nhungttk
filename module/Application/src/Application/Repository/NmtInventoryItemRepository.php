<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NmtInventoryItemRepository extends EntityRepository
{

    /** @var \Application\Entity\NmtInventoryItem $e*/
    // @ORM\Entity(repositoryClass="Application\Repository\NmtInventoryItemRepository")
    private $sql = "SELECT * FROM nmt_inventory_item ";

    private $sql_cat_album = "
SELECT 
	nmt_inventory_item_category_member.*,
	nmt_inventory_item_category.node_name as category_name,
	nmt_inventory_item.item_name,
	nmt_inventory_item.item_sku,
    nmt_inventory_item.location,
	nmt_inventory_item.token AS item_token,
	nmt_inventory_item.checksum AS item_checksum,
	nmt_inventory_item_picture.folder_relative,
	nmt_inventory_item_picture.filename,
	nmt_inventory_item_picture.id AS pic_id,
	nmt_inventory_item_picture.token AS pic_token,
	nmt_inventory_item_picture.checksum AS pic_checksum
FROM nmt_inventory_item_category_member
LEFT JOIN
(
	SELECT nmt_inventory_item_picture.item_id, MAX(nmt_inventory_item_picture.id) AS picture_id  FROM nmt_inventory_item_picture
		WHERE nmt_inventory_item_picture.is_active=1
	GROUP BY nmt_inventory_item_picture.item_id
)
AS picture
ON picture.item_id = nmt_inventory_item_category_member.item_id

LEFT JOIN nmt_inventory_item_picture
ON nmt_inventory_item_picture.id = picture.picture_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_inventory_item_category_member.item_id

LEFT JOIN nmt_inventory_item_category
ON nmt_inventory_item_category.node_id = nmt_inventory_item_category_member.category_id

WHERE 1
";

    private $sql_item_price = "
SELECT
	nmt_inventory_item.*,
	nmt_inventory_trx_last.last_trx_id,
    nmt_inventory_item_purchasing_last.last_purchasing_id,
    IFNULL(nmt_inventory_trx.vendor_item_unit,nmt_inventory_item_purchasing.vendor_item_unit) AS vendor_item_unit,
    IFNULL(nmt_inventory_trx.vendor_unit_price,nmt_inventory_item_purchasing.vendor_unit_price) AS vendor_unit_price,
    IFNULL(nmt_application_currency.currency,nmt_application_currency_1.currency) AS currency,
	IFNULL(nmt_bp_vendor.vendor_name,nmt_bp_vendor_1.vendor_name) AS vendor_name,
    IFNULL(nmt_bp_vendor.token,nmt_bp_vendor_1.token) AS vendor_token,
    IFNULL(nmt_bp_vendor.checksum,nmt_bp_vendor_1.checksum) AS vendor_checksum
FROM nmt_inventory_item
LEFT JOIN
(
   SELECT
		nmt_inventory_trx.item_id,
		MAX(nmt_inventory_trx.id) AS last_trx_id
	FROM nmt_inventory_trx
    WHERE nmt_inventory_trx.is_active =1
    GROUP BY nmt_inventory_trx.item_id
  ) 
AS nmt_inventory_trx_last
ON nmt_inventory_trx_last.item_id=nmt_inventory_item.id

LEFT JOIN nmt_inventory_trx
ON nmt_inventory_trx.id = nmt_inventory_trx_last.last_trx_id

LEFT JOIN nmt_bp_vendor
ON nmt_bp_vendor.id = nmt_inventory_trx.vendor_id

LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = nmt_inventory_trx.currency_id

LEFT JOIN
(
   SELECT
		nmt_inventory_item_purchasing.item_id,
		MAX(nmt_inventory_item_purchasing.id) AS last_purchasing_id
	FROM nmt_inventory_item_purchasing
    WHERE nmt_inventory_item_purchasing.is_active =1
    GROUP BY nmt_inventory_item_purchasing.item_id
  ) 
AS nmt_inventory_item_purchasing_last
ON nmt_inventory_item_purchasing_last.item_id=nmt_inventory_item.id

LEFT JOIN nmt_inventory_item_purchasing
ON nmt_inventory_item_purchasing.id = nmt_inventory_item_purchasing_last.last_purchasing_id

LEFT JOIN nmt_bp_vendor AS nmt_bp_vendor_1
ON nmt_bp_vendor_1.id = nmt_inventory_item_purchasing.vendor_id

LEFT JOIN nmt_application_currency AS nmt_application_currency_1
ON nmt_application_currency_1.id = nmt_inventory_item_purchasing.currency_id

WHERE 1



";

    private $sql_item_last_trx = "

SELECT
nmt_inventory_trx_max.trx_total_record,
nmt_inventory_trx.*

FROM
nmt_inventory_trx
JOIN
(
	SELECT
		nmt_inventory_trx.item_id,
		MAX(nmt_inventory_trx.id) AS max_id,	
		MAX(nmt_inventory_trx.trx_date) AS max_date,
       count(nmt_inventory_trx.id) as trx_total_record

	FROM nmt_inventory_trx
    
    WHERE nmt_inventory_trx.flow='IN' AND nmt_inventory_trx.is_active=1
	GROUP BY nmt_inventory_trx.item_id 
)
AS nmt_inventory_trx_max
ON nmt_inventory_trx_max.item_id = nmt_inventory_trx.item_id 
AND nmt_inventory_trx.trx_date = nmt_inventory_trx_max.max_date
AND nmt_inventory_trx.id = nmt_inventory_trx_max.max_id

WHERE 1

";

    private $sql_item_purchasing = "
    SELECT
    nmt_inventory_item_purchasing_max.purchasing_total_record,
    nmt_inventory_item_purchasing.*
    FROM
    nmt_inventory_item_purchasing
    JOIN
    (
        SELECT
        nmt_inventory_item_purchasing.item_id,
        MAX(nmt_inventory_item_purchasing.id) AS max_id,
        MAX(nmt_inventory_item_purchasing.created_on) AS max_date,
        count(nmt_inventory_item_purchasing.id) as purchasing_total_record
        FROM nmt_inventory_item_purchasing
        
        WHERE nmt_inventory_item_purchasing.is_active=1
        GROUP BY nmt_inventory_item_purchasing.item_id
        )
        AS nmt_inventory_item_purchasing_max
        ON nmt_inventory_item_purchasing_max.item_id = nmt_inventory_item_purchasing.item_id
        AND nmt_inventory_item_purchasing_max.max_date = nmt_inventory_item_purchasing.created_on
        AND nmt_inventory_item_purchasing_max.max_id = nmt_inventory_item_purchasing.id
        WHERE 1
    ";

    /**
     *
     * @param int $item_id
     * @param string $item_token
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL|NULL
     *
     */
    public function getItem($item_id = null, $item_token = '')
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
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_pr_row", "total_pr_row");
            $rsm->addScalarResult("total_picture", "total_picture");
            $rsm->addScalarResult("total_attachment", "total_attachment");
            $rsm->addScalarResult("total_po_row", "total_po_row");
            $rsm->addScalarResult("total_ap_row", "total_ap_row");
            $rsm->addScalarResult("total_qo_row", "total_qo_row");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get Most Ordered Items
     *
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     *
     */
    public function getMostOrderItems($limit = 50, $offset = 0)
    {
        $sql_tmp = "
SELECT
	nmt_inventory_item.*,
  	COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) AS total_pr_row
FROM nmt_inventory_item
LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr_row.item_id = nmt_inventory_item.id
group by nmt_inventory_item.id
order by COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) DESC LIMIT %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_pr_row", "total_pr_row");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get Last AP Row
     *
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     *
     */
    public function getLastAPRows($limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT
	fin_vendor_invoice_row.*
	FROM fin_vendor_invoice_row
    where fin_vendor_invoice_row.current_state='finalInvoice'
	ORDER BY fin_vendor_invoice_row.created_on DESC LIMIT  %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     *
     */
    public function getRandomItem()
    {
        $sql_tmp = "
SELECT * FROM nmt_inventory_item_picture ORDER BY RAND() LIMIT 0,1 ";
        // $sql=sprintf($sql_tmp,$limit);
        $sql = $sql_tmp;
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemPicture', 'nmt_inventory_item_picture');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get Last created Items
     *
     * @param number $limit
     * @param number $offset
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     *
     */
    public function getLastCreatedItems($limit = 100, $offset = 0)
    {
        $sql_tmp = "
select
    nmt_inventory_item.*
    From nmt_inventory_item
order by nmt_inventory_item.created_on desc LIMIT %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param string $item_type
     * @param int $is_active
     * @param int $is_fixed_asset
     * @param string $sort_by
     * @param string $sort
     * @param number $limit
     * @param number $offset
     * @return array
     *
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
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param string $item_type
     * @param boolean $is_active
     * @param boolean $is_fixed_asset
     * @return mixed
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
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_row", "total_row");
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return (int) $result['total_row'];
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     *
     * @param int $cat_id
     * @return array
     */
    public function getAlbum($cat_id)
    {
        $sql = $this->sql_cat_album;

        if ($cat_id > 0) {
            $sql = $sql . " AND nmt_inventory_item_category_member.category_id=" . $cat_id;
        }
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param string $item_type
     * @param boolean $is_active
     * @param boolean $is_fixed_asset
     * @param string $sort_by
     * @param string $sort
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getItemPrice($item_type = null, $is_active = null, $is_fixed_asset = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = $this->sql_item_price;

        if ($item_type == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
            $sql = $sql . " AND nmt_inventory_item.item_type =" . $item_type;
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
        } elseif ($sort_by == "vendorName") {
            $sql = $sql . " ORDER BY IFNULL(nmt_bp_vendor.vendor_name,nmt_bp_vendor_1.vendor_name) " . $sort;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $sql = $sql . ";";

        // echo $sql;

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param number $item_id
     * @return array|NULL
     */
    public function getItemLastTrx($item_id = 0)
    {
        $sql = $this->sql_item_last_trx;

        if ($item_id > 0) {
            $sql = $sql . " AND nmt_inventory_trx.item_id=. $item_id ";
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("trx_total_record", "trx_total_record");
            // $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            // $rsm->addScalarResult("processing_quantity", "processing_quantity");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @param number $item_id
     * @return array|NULL
     */
    public function getItemPurchasing($item_id = 0)
    {
        $sql = $this->sql_item_purchasing;

        if ($item_id > 0) {
            $sql = $sql . " AND nmt_inventory_item_purchasing.item_id=. $item_id ";
        }

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemPurchasing', 'nmt_inventory_item_purchasing');
            $rsm->addScalarResult("purchasing_total_record", "purchasing_total_record");
            // $rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            // $rsm->addScalarResult("processing_quantity", "processing_quantity");

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get Vacant Serial Number
     *
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL|NULL
     */
    public function getVacantSerialNumbers()
    {
        $sql_tmp = "
select
*
from nmt_inventory_serial
where nmt_inventory_serial.is_active=%s AND nmt_inventory_serial.item_id is null";

        $sql = sprintf($sql_tmp, 1);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventorySerial', 'nmt_inventory_serial');
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

   /**
    * 
    * @param integer $item_id
    * @return array
    */
    public function getAllItemWithSerial($item_id =null)
    {
        $sql = 
'SELECT 
nmt_inventory_item_serial.id AS serial_id,
nmt_inventory_item_serial.serial_number as serial_no,
nmt_inventory_item_serial.serial_number_1 as serial_no1,
nmt_inventory_item_serial.serial_number_2 as serial_no2,

nmt_inventory_item_serial.mfg_name,
nmt_inventory_item_serial.mfg_description,
nmt_inventory_item_serial.remarks as serial_remarks,
nmt_inventory_item_serial.mfg_serial_number,
nmt_inventory_item_serial.mfg_model,
nmt_inventory_item_serial.mfg_model1,
nmt_inventory_item_serial.mfg_model2,
nmt_inventory_item.*
FROM nmt_inventory_item
LEFT JOIN nmt_inventory_item_serial
ON nmt_inventory_item_serial.item_id = nmt_inventory_item.id

Where 1 and nmt_inventory_item.is_active=1
';
        if($item_id > 0){
            $sql = $sql. ' and nmt_inventory_item.id = '. $item_id;
        }
        
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @todo GOODS RECEIPT
     */
    public function getMovement($id, $token)
    {
        $sql = "
SELECT
    nmt_inventory_mv.*,
    Count(nmt_inventory_trx.id) as total_row,
  	COUNT(CASE WHEN nmt_inventory_trx.is_active =1 THEN (nmt_inventory_trx.id) ELSE NULL END) AS active_row
             
FROM nmt_inventory_mv
LEFT JOIN nmt_inventory_trx
ON nmt_inventory_trx.movement_id = nmt_inventory_mv.id
WHERE 1
";

        $sql = sprintf($sql . " AND nmt_inventory_mv.id = %s AND nmt_inventory_mv.token='%s' Group BY nmt_inventory_trx.movement_id", $id, $token);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryMv', 'nmt_inventory_mv');
            $rsm->addScalarResult("total_row", "total_row");
            $rsm->addScalarResult("active_row", "active_row");
            $rsm->addScalarResult("total_row", "total_row");
            // echo $sql;

            $query = $this->_em->createNativeQuery($sql, $rsm);

            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @todo GOODS RECEIPT
     */
    public function getItemStock($id, $token)
    {
        $sql = \Application\Repository\SQL\NmtInventoryItemSQL::ITEM_STOCK_SQL;
        $sql1 = " AND nmt_inventory_item.id=" . $id;

        $sql = sprintf($sql, $sql1, $sql1, $sql1, $sql1);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_gr", "total_gr");
            $rsm->addScalarResult("total_gr_value", "total_gr_value");
            $rsm->addScalarResult("total_onhand", "total_onhand");
            $rsm->addScalarResult("total_onhand_value", "total_onhand_value");
            $rsm->addScalarResult("total_onhand_local_value", "total_onhand_local_value");

            $rsm->addScalarResult("total_gi", "total_gi");
            $rsm->addScalarResult("total_gi_value", "total_gi_value");
            $rsm->addScalarResult("total_ex", "total_ex");

            // echo $sql;

            $query = $this->_em->createNativeQuery($sql, $rsm);

            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}

