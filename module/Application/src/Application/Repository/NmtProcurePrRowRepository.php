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
	
	private $sql = "
SELECT
	nmt_procure_pr_row.*,
    nmt_inventory_item.item_name,
	nmt_inventory_item.checksum as item_checksum,
	nmt_inventory_item.token as item_token,
	
    nmt_procure_pr.checksum as pr_checksum,
	nmt_procure_pr.token as pr_token,
	nmt_procure_pr.pr_number,
		
	IFNULL(nmt_inventory_trx.total_received,0) AS total_received,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0
    ,(nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))
    ,0) AS confirmed_balance,
    
      IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>=0
    ,0,(nmt_procure_pr_row.quantity*-1 + IFNULL(nmt_inventory_trx.total_received,0))) AS confirmed_free_balance
 
	
    
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
	
	
	private $sql1 = "
SELECT
	nmt_procure_pr.id ,
    nmt_procure_pr.pr_number,
    nmt_procure_pr.created_on,
    nmt_procure_pr.last_change_on,
    nmt_procure_pr.is_active,
    nmt_procure_pr.is_draft,
    
    nmt_procure_pr.checksum as pr_checksum,
	nmt_procure_pr.token as pr_token,
	year(nmt_procure_pr.created_on) as pr_year,
	month(nmt_procure_pr.created_on) as pr_month,
    ifnull(nmt_procure_pr_row.total_row, 0) as total_row,
    ifnull(nmt_procure_pr_row.row_completed, 0) as row_completed,
    ifnull(nmt_procure_pr_row.row_completed_converted, 0) as row_completed_converted,
    
    ifnull(nmt_procure_pr_row.row_pending, 0) as row_pending,
    
    ifnull(nmt_procure_pr_row.percentage_completed, 0) as percentage_completed,
    ifnull(nmt_procure_pr_row.percentage_completed_converted, 0) as percentage_completed_converted
    
    
FROM nmt_procure_pr

Left JOIN
(
	SELECT
	nmt_procure_pr_row.pr_id,
	Count(nmt_procure_pr_row.id) as total_row,
	sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))<=0 THEN  1 ELSE 0 END) AS row_completed,
    sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received_converted,0))<=0 THEN  1 ELSE 0 END) AS row_completed_converted,
	sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0 THEN  1 ELSE 0 END) AS row_pending,
	(sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))<=0 THEN  1 ELSE 0 END)/Count(nmt_procure_pr_row.id)) as percentage_completed,
    (sum(CASE WHEN (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received_converted,0))<=0 THEN  1 ELSE 0 END)/Count(nmt_procure_pr_row.id)) as percentage_completed_converted

	from nmt_procure_pr_row
	LEFT JOIN
	(
		SELECT
			nmt_inventory_trx.pr_row_id AS pr_row_id,
			SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity ELSE 0 END) AS total_received,
			SUM(CASE WHEN nmt_inventory_trx.flow='IN' THEN  nmt_inventory_trx.quantity*nmt_inventory_trx.conversion_factor ELSE 0 END) AS total_received_converted

		FROM nmt_inventory_trx
		GROUP BY nmt_inventory_trx.pr_row_id
	) 
	AS nmt_inventory_trx
	ON nmt_procure_pr_row.id = nmt_inventory_trx.pr_row_id
	Group by nmt_procure_pr_row.pr_id
) 
AS nmt_procure_pr_row
ON nmt_procure_pr_row.pr_id = nmt_procure_pr.id

where 1

";
	
	/**
	 *
	 * @param number $limit        	
	 * @param number $offset        	
	 * @return array
	 */
	public function getAllPrRow($pr_year = 0, $balance = null,$sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = $this->sql;
		
		if ($pr_year > 0) {
			$sql = $sql . " AND year(nmt_procure_pr.created_on) =" . $pr_year;
		}
		
		if ($balance == 0) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
		}
		
		if ($sort_by == "itemName") {
			$sql = $sql. " ORDER BY nmt_inventory_item.item_name " . $sort;
		}elseif ($sort_by == "prNumber") {
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
	
	/**
	 *
	 * @param number $limit
	 * @param number $offset
	 * @return array
	 */
	public function getPrRow($pr_id, $balance = null,$sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = $this->sql;
		
		$sql = $sql . " AND nmt_procure_pr_row.pr_id =" . $pr_id;
	
		if ($balance == 0) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) <= 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) < 0";
		}
		
		if ($sort_by == "itemName") {
			$sql = $sql. " ORDER BY nmt_inventory_item.item_name " . $sort;
		}elseif($sort_by == "createdOn") {
			$sql = $sql. " ORDER BY nmt_procure_pr_row.created_on " . $sort;
		}elseif($sort_by == "balance") {
			$sql = $sql. " ORDER BY (nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0)) " . $sort;
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
	
	/**
	 *
	 * @param number $limit
	 * @param number $offset
	 * @return array
	 */
	public function getPrList($is_active = null, $balance = null, $sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = $this->sql1;
		
		if ($is_active== 1) {
			$sql = $sql. " AND nmt_procure_pr.is_active=  1";
		}elseif($is_active== -1) {
			$sql = $sql. " AND nmt_procure_pr.is_active = 0";
		}
	
		// Group
		
		// fullfiled
		if ($balance == 0) {
			$sql = $sql. " AND ifnull(nmt_procure_pr_row.total_row, 0)	<=ifnull(nmt_procure_pr_row.row_completed, 0)";
		}elseif($balance == 1){
			$sql = $sql. " AND ifnull(nmt_procure_pr_row.total_row, 0)	> ifnull(nmt_procure_pr_row.row_completed, 0)";
			
		}
		
		if ($sort_by == "prNumber") {
			$sql = $sql. " ORDER BY nmt_procure_pr.pr_number " . $sort;
		}elseif($sort_by == "createdOn") {
			$sql = $sql. " ORDER BY nmt_procure_pr.created_on " . $sort;
		}elseif($sort_by == "completion") {
			$sql = $sql. " ORDER BY ifnull(nmt_procure_pr_row.percentage_completed, 0) " . $sort;
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

