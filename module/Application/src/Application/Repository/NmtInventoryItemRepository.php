<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\NmtInventoryItem;

/**
 *
 * @author nmt
 *        
 */
class NmtInventoryItemRepository extends EntityRepository {
	// @ORM\Entity(repositoryClass="Application\Repository\NmtInventoryItemRepository")
	private $sql = "SELECT * FROM nmt_inventory_item ";
	
	private $sql_cat_album="
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
	public function getItems($item_type = null, $is_active = null, $is_fixed_asset = null, $sort_by = null, $sort = null, $limit = 0, $offset = 0) {
		$sql = "SELECT * FROM nmt_inventory_item";
		
		//when paginator needed
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
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ();
	}
	
	/**
	 *
	 * @param unknown $item_type        	
	 * @param unknown $is_active        	
	 * @param unknown $is_fixed_asset        	
	 * @return mixed
	 */
	public function getTotalItem($item_type = null, $is_active = null, $is_fixed_asset = null) {
		$sql = "SELECT count(id) as total_row FROM nmt_inventory_item Where 1";
		
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
		
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return ( int ) $stmt->fetchAll () [0] ['total_row'];
	}

    public function getAlbum($cat_id){
        
        $sql = $this->sql_cat_album;
        
        if($cat_id>0){
            $sql=$sql. " AND nmt_inventory_item_category_member.category_id=" . $cat_id;
        }
        $stmt = $this->_em->getConnection ()->prepare ( $sql );
        $stmt->execute ();
        return $stmt->fetchAll ();
        
    }

}

;