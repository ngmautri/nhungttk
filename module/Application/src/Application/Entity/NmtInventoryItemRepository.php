<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author nmt
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

    private $sql_item_last_trx= "

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
    
    
    private $sql_item_purchasing="
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
     * @param unknown $item_type
     * @param unknown $is_active
     * @param unknown $is_fixed_asset
     * @param unknown $sort_by
     * @param unknown $sort
     * @param number $limit
     * @param number $offset
     * @return array
     */
    public function getItems($item_type = null, $is_active = null, $is_fixed_asset = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM nmt_inventory_item";
        
        // when paginator needed
        if ($limit > 0) {
            $sql1 = "SELECT id from nmt_inventory_item";
            
            $sql1 = $sql1 . " WHERE 1";
            
            if ($item_type == "ITEM" || $item_type == "SERVICE" || $item_type == "SOFTWARE") {
                $sql1 = $sql1 . " AND nmt_inventory_item.item_type =" . $item_type;
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
            }
        }
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * @param unknown $item_type
     * @param unknown $is_active
     * @param unknown $is_fixed_asset
     * @return mixed
     */
    public function getTotalItem($item_type = null, $is_active = null, $is_fixed_asset = null)
    {
        $sql = "SELECT count(*) as total_row FROM nmt_inventory_item Where 1 ";
        
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
     * @param unknown $cat_id
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
     * @param unknown $item_type
     * @param unknown $is_active
     * @param unknown $is_fixed_asset
     * @param unknown $sort_by
     * @param unknown $sort
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
    public function getItemLastTrx($item_id=0)
    {
        $sql = $this->sql_item_last_trx;
        
        if($item_id>0){
            $sql = $sql . " AND nmt_inventory_trx.item_id=. $item_id ";
        }
         
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryTrx', 'nmt_inventory_trx');
            $rsm->addScalarResult("trx_total_record", "trx_total_record");
            //$rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            //$rsm->addScalarResult("processing_quantity", "processing_quantity");
            
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
    public function getItemPurchasing($item_id=0)
    {
        $sql = $this->sql_item_purchasing;
        
        if($item_id>0){
            $sql = $sql . " AND nmt_inventory_item_purchasing.item_id=. $item_id ";
        }
        
        try {
            $rsm = new ResultSetMappingBuilder($this->_em);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemPurchasing', 'nmt_inventory_item_purchasing');
            $rsm->addScalarResult("purchasing_total_record", "purchasing_total_record");
            //$rsm->addScalarResult("confirmed_free_balance", "confirmed_free_balance");
            //$rsm->addScalarResult("processing_quantity", "processing_quantity");
            
            $query = $this->_em->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
}
