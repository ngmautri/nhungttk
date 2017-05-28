<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author nmt
 *        
 */
class NmtProcurePrRowRepository extends EntityRepository {
	// @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePrRowRepository")
	
	/**
	 *
	 * @param number $limit        	
	 * @param number $offset        	
	 * @return array
	 */
	public function getAllPrRow($sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = "
SELECT
	nmt_procure_pr_row.*,
    nmt_inventory_item.item_name,
	nmt_inventory_item.checksum as item_checksum,
	nmt_inventory_item.token as item_token,
	
    nmt_procure_pr.checksum as pr_checksum,
	nmt_procure_pr.token as pr_token,
	nmt_procure_pr.pr_number,
		
	IFNULL(nmt_inventory_trx.total_received,0) AS total_received
FROM nmt_procure_pr_row
left join nmt_inventory_item
on nmt_inventory_item.id = nmt_procure_pr_row.item_id

left join nmt_procure_pr
on nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN
(
SELECT
	nmt_inventory_trx.pr_row_id AS pr_row_id,
	SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received 
FROM nmt_inventory_trx
GROUP BY nmt_inventory_trx.pr_row_id
) 
AS nmt_inventory_trx
ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id
WHERE 1




   	 	";
		
		if ($sort_by == "itemName") {
			$sql = $sql. " ORDER BY nmt_inventory_item.item_name " . $sort;
		}
		
		if ($sort_by == "prNumber") {
			$sql = $sql. " ORDER BY nmt_procure_pr.pr_number " . $sort;
		}
		
		if($limit>0){
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if($offset>0){
			$sql = $sql. " OFFSET " . $offset;
		}
		$sql = $sql.";";
		
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ();
	}
}

