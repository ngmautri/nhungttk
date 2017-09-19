SELECT
	nmt_procure_pr_row.*,
    nmt_inventory_item.item_name,
	nmt_inventory_item.checksum as item_checksum,
	nmt_inventory_item.token as item_token,
	
    nmt_procure_pr.checksum as pr_checksum,
	nmt_procure_pr.token as pr_token,
	nmt_procure_pr.pr_number,
    year(nmt_procure_pr.created_on) as pr_year,
		
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

