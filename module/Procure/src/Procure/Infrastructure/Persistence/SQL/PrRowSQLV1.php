<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class PrRowSQLV1
{

    const SELECT_QO_KEY = "/*select_qo*/";

    const SELECT_PO_KEY = "/*select_po*/";

    const SELECT_GR_KEY = "/*select_gr*/";

    const SELECT_STOCK_GR_KEY = "/*select_stock_gr*/";

    const SELECT_AP_KEY = "/*select_ap*/";

    const SELECT_LAST_AP_KEY = "/*select_last_ap*/";

    const JOIN_QO_KEY = "/*join_qo*/";

    const JOIN_PO_KEY = "/*join_po*/";

    const JOIN_GR_KEY = "/*join_gr*/";

    const JOIN_STOCK_GR_KEY = "/*join_stock_gr*/";

    const JOIN_AP_KEY = "/*join_ap*/";

    const JOIN_LAST_AP_KEY = "/*join_last_ap*/";

    const PR_ROW_SQL_TEMPLATE = "
SELECT

	/*FIXED PART */
	nmt_procure_pr_row.*,
	
	/*Select QO*/    
	/*select_qo*/
    
	/*Select PO*/    
	/*select_po*/
     
	/*Select GR*/    
	/*select_gr*/
   
	/*Select Stock GR */    
	/*select_stock_gr*/
    
	/*Select AP */        
	/*select_ap*/      

	/*Select Last AP */        
	/*select_last_ap*/  
    
	nmt_procure_pr.pr_name,
	nmt_procure_pr.created_on AS pr_created_on,
	YEAR(nmt_procure_pr.submitted_on) AS pr_year,
	nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,  
	IFNULL(nmt_procure_pr_row.converted_standard_quantity,0) AS standard_pr_qty
  
FROM nmt_procure_pr_row
       
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

/*Join QO */    
/*join_qo*/    
    
/*Join PO */    
/*join_po*/    
     
/*Join GR */    
/*join_gr*/    
    
/*Join STOCK GR */    
/*join_stock_gr*/    
    
/*Join AP */        
/*join_ap*/    

/*Join Last AP */        
/*join_last_ap*/    
        
WHERE 1 %s
";

    const PR_QO_SQL = "
LEFT JOIN
(
    SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN  nmt_procure_qo_row.quantity ELSE 0 END) AS qo_qty,
		SUM(CASE WHEN nmt_procure_qo_row.is_active =1 AND  nmt_procure_qo_row.is_posted =1 THEN  nmt_procure_qo_row.quantity ELSE 0 END) AS posted_qo_qty,
		SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN  nmt_procure_qo_row.converted_standard_quantity ELSE 0 END) AS standard_qo_qty,
		SUM(CASE WHEN nmt_procure_qo_row.is_active =1 AND  nmt_procure_qo_row.is_posted =1 THEN  nmt_procure_qo_row.converted_standard_quantity ELSE 0 END) AS posted_standard_qo_qty
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_pr_row.item_id


	JOIN nmt_procure_qo_row
	ON nmt_procure_qo_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
	GROUP BY nmt_procure_qo_row.pr_row_id
)
AS nmt_procure_qo_row
ON nmt_procure_qo_row.pr_row_id = nmt_procure_pr_row.id
";

    const PR_PO_SQL = "
LEFT JOIN
(
    SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.converted_standard_quantity ELSE 0 END) AS standard_po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.converted_standard_quantity ELSE 0 END) AS posted_standard_po_qty
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
    
    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_pr_row.item_id


	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
	GROUP BY nmt_procure_po_row.pr_row_id
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id   
";

    const PR_POGR_SQL = "
LEFT JOIN
(
 
  SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.converted_standard_quantity ELSE 0 END) AS standard_gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.converted_standard_quantity ELSE 0 END) AS posted_standard_gr_qty
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
    
    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_pr_row.item_id


	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
	GROUP BY nmt_procure_gr_row.pr_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
";

    const PR_AP_SQL = "
LEFT JOIN
(
  SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_draft =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.converted_standard_quantity ELSE 0 END) AS standard_ap_qty,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.converted_standard_quantity ELSE 0 END) AS posted_standard_ap_qty
	
 FROM nmt_procure_pr_row
    
    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
        
	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
    GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

";

    const PR_STOCK_GR_SQL = "
LEFT JOIN
(
 SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND  nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.converted_standard_quantity ELSE 0 END) AS standard_stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND  nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.converted_standard_quantity ELSE 0 END) AS posted_standard_stock_gr_qty
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id   
";

    const ITEM_LAST_AP_SQL = "
LEFT JOIN
(
  SELECT
	fin_vendor_invoice_row.item_id,
	fin_vendor_invoice_row.unit_price,
    fin_vendor_invoice_row.converted_standard_unit_price,
    fin_vendor_invoice_row.standard_convert_factor,
	fin_vendor_invoice.vendor_name,
	fin_vendor_invoice.currency_iso3,
    fin_vendor_invoice.doc_date
	FROM
	fin_vendor_invoice_row

	INNER JOIN
	(
	SELECT
		MAX(fin_vendor_invoice_row.id) AS max_id,
		fin_vendor_invoice_row.item_id AS item_id
		FROM fin_vendor_invoice_row

	INNER JOIN fin_vendor_invoice ON
	fin_vendor_invoice_row.invoice_id = fin_vendor_invoice.id
	WHERE fin_vendor_invoice.doc_status='posted'
	GROUP BY fin_vendor_invoice_row.item_id
	)
	AS last_ap
	ON last_ap.max_id = fin_vendor_invoice_row.id AND fin_vendor_invoice_row.item_id = last_ap.item_id

	LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id   
";
}
