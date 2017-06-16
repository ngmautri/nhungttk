<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
//use Application\Entity\NmtInventoryItem;

/**
 *
 * @author nmt
 *        
 */
class NmtInventoryItemRepository extends EntityRepository {
	// @ORM\Entity(repositoryClass="Application\Repository\NmtInventoryItemRepository")
	/**
	 *
	 * @return array
	 */
	
	public function getAll() {
		$sql = "
SELECT
	nmt_procure_pr_row.*,
    nmt_inventory_item.item_name,
	IFNULL(nmt_inventory_trx.total_received,0) AS total_received
FROM nmt_procure_pr_row
left join nmt_inventory_item
on nmt_inventory_item.id = nmt_procure_pr_row.item_id

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
		
		$stmt = $this->_em->getConnection ()->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ();
	}
}

