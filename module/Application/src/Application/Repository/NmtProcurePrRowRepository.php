<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\NmtProcurePrRow;

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
    nmt_inventory_item.item_sku,
	nmt_inventory_item.checksum AS item_checksum,
	nmt_inventory_item.token AS item_token,
	
    nmt_procure_pr.checksum AS pr_checksum,
	nmt_procure_pr.token AS pr_token,
	nmt_procure_pr.pr_number,
    nmt_procure_pr.submitted_on,
    
    ifnull(nmt_inventory_trx_last.vendor_name,nmt_inventory_item_purchasing.vendor_name) as vendor_name,
	ifnull(nmt_inventory_trx_last.vendor_id,nmt_inventory_item_purchasing.vendor_id) as vendor_id,
    ifnull(nmt_inventory_trx_last.vendor_token,nmt_inventory_item_purchasing.vendor_token) as vendor_token,
	ifnull(nmt_inventory_trx_last.vendor_checksum,nmt_inventory_item_purchasing.vendor_checksum) as vendor_checksum,
    
 	ifnull( nmt_inventory_trx_last.vendor_unit_price, nmt_inventory_item_purchasing.vendor_unit_price) as vendor_unit_price,
  	ifnull( nmt_inventory_trx_last.currency, nmt_inventory_item_purchasing.currency) as currency,
 	ifnull( nmt_inventory_trx_last.vendor_item_unit, nmt_inventory_item_purchasing.vendor_item_unit) as vendor_item_unit,
 		
	IFNULL(nmt_inventory_trx.total_received,0) AS total_received,
    
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>0
    ,(nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))
    ,0) AS confirmed_balance,
    IF ((nmt_procure_pr_row.quantity - IFNULL(nmt_inventory_trx.total_received,0))>=0
    ,0,(nmt_procure_pr_row.quantity*-1 + IFNULL(nmt_inventory_trx.total_received,0))) AS confirmed_free_balance
 
    
FROM nmt_procure_pr_row
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

left JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

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

LEFT JOIN
(
	SELECT
	nmt_bp_vendor.vendor_name,
	nmt_bp_vendor.token as vendor_token,
    nmt_bp_vendor.checksum as vendor_checksum, 
	nmt_application_currency.currency,
	nmt_inventory_trx.*,
	COUNT(nmt_inventory_trx.item_id) AS total_trx,
	MAX(nmt_inventory_trx.trx_date) AS last_trx
	FROM nmt_inventory_trx

	LEFT JOIN nmt_bp_vendor
	ON nmt_bp_vendor.id = nmt_inventory_trx.vendor_id

	LEFT JOIN nmt_application_currency
	ON nmt_application_currency.id = nmt_inventory_trx.currency_id
	WHERE nmt_inventory_trx.is_active=1
    GROUP BY nmt_inventory_trx.item_id
    ORDER BY nmt_inventory_trx.trx_date DESC
	
)
AS nmt_inventory_trx_last
ON nmt_inventory_trx_last.item_id = nmt_procure_pr_row.item_id

LEFT JOIN
(

	SELECT
	nmt_bp_vendor.vendor_name,
    nmt_bp_vendor.token as vendor_token,
    nmt_bp_vendor.checksum as vendor_checksum,
    
	nmt_application_currency.currency,
	nmt_inventory_item_purchasing.*,
	COUNT(nmt_inventory_item_purchasing.item_id) AS total_purchase,
	MAX(nmt_inventory_item_purchasing.created_on) AS last_purchase
	FROM nmt_inventory_item_purchasing

	LEFT JOIN nmt_bp_vendor
	ON nmt_bp_vendor.id = nmt_inventory_item_purchasing.vendor_id

	LEFT JOIN nmt_application_currency
	ON nmt_application_currency.id = nmt_inventory_item_purchasing.currency_id

	WHERE nmt_inventory_item_purchasing.is_active=1
	GROUP BY nmt_inventory_item_purchasing.item_id
)
AS nmt_inventory_item_purchasing
ON nmt_inventory_item_purchasing.item_id = nmt_procure_pr_row.item_id
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
	
	private $sql_project_item="
SELECT
nmt_procure_pr_row.id as pr_row_id,
nmt_procure_pr_row.checksum AS pr_row_checksum,
nmt_procure_pr_row.token AS pr_row_token,

nmt_procure_pr_row.pr_id,
nmt_procure_pr.pr_number,
nmt_procure_pr.checksum AS pr_checksum,
nmt_procure_pr.token AS pr_token,
nmt_procure_pr_row.item_id,
nmt_inventory_item.item_name,
nmt_inventory_item.item_sku,
nmt_inventory_item.checksum AS item_checksum,
nmt_inventory_item.token AS item_token,
nmt_inventory_trx.quantity,
nmt_inventory_trx.vendor_unit_price,
nmt_inventory_trx.quantity*vendor_unit_price AS total_price,
nmt_application_currency.currency,
nmt_procure_pr_row.remarks,
nmt_procure_pr_row.fa_remarks,
nmt_inventory_trx.vendor_id,
nmt_bp_vendor.vendor_name,
nmt_bp_vendor.checksum as vendor_checksum,
nmt_bp_vendor.token as vendor_token
FROM nmt_procure_pr_row
LEFT JOIN nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = nmt_inventory_trx.currency_id

LEFT JOIN nmt_bp_vendor
ON nmt_bp_vendor.id = nmt_inventory_trx.vendor_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
WHERE 1

";
	
	/**
	 *
	 * @param number $limit        	
	 * @param number $offset        	
	 * @return array
	 */
	public function getAllPrRow($is_active=1,$pr_year = 0, $balance = null,$sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = $this->sql;
		
		if ($is_active == 1) {
		    $sql = $sql . " AND (nmt_procure_pr.is_active = 1 OR nmt_procure_pr_row.is_active = 1)";
		}elseif($is_active == -1) {
		    $sql = $sql . " AND (nmt_procure_pr.is_active = 0 OR nmt_procure_pr_row.is_active = 0)";
		}
		
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
		}elseif ($sort_by == "vendorName") {
			$sql = $sql. " ORDER BY ifnull(nmt_inventory_trx_last.vendor_name,nmt_inventory_item_purchasing.vendor_name) " . $sort;
		}elseif ($sort_by == "currency") {
			$sql = $sql. " ORDER BY ifnull( nmt_inventory_trx_last.currency, nmt_inventory_item_purchasing.currency) " . $sort;
		}elseif ($sort_by == "unitPrice") {
			$sql = $sql. " ORDER BY ifnull( nmt_inventory_trx_last.vendor_unit_price, nmt_inventory_item_purchasing.vendor_unit_price) " . $sort;
		}elseif ($sort_by == "balance") {
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
		}elseif($sort_by == "prSubmitted") {
			$sql = $sql. " ORDER BY nmt_procure_pr.submitted_on" . $sort;
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
	public function getPrList($row_number =1, $is_active = null, $balance = null, $sort_by = null, $sort=null, $limit = 0, $offset = 0) {
		$sql = $this->sql1;
		
		if ($row_number== 1) {
		    $sql = $sql. " AND ifnull(nmt_procure_pr_row.total_row, 0) > 0";
		}elseif($row_number == 0) {
		    $sql = $sql. " AND ifnull(nmt_procure_pr_row.total_row, 0) = 0";
		}
		
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
	
	
	/**
	 * 
	 * @param unknown $project_id
	 * @return array
	 */
	public function getProjectItem($project_id) {
	    $sql = $this->sql_project_item;
	    
	    $sql = $sql. " AND nmt_procure_pr_row.project_id=" . $project_id;
	    $sql = $sql.";";
	    
	    $stmt = $this->_em->getConnection ()->prepare ( $sql );
	    $stmt->execute ();
	    return $stmt->fetchAll ();
	}
	
	
}

