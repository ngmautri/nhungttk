<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowSQL
{

    const SELECT_QO_KEY = "#select_qo";

    const SELECT_PO_KEY = "#select_po";

    const PR_ROW_SQL_TEMPLATE = "
SELECT

    /* FIXED PART */
	nmt_procure_pr_row.*,
        
	nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,
    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,

   /* PO */    
    #select_qo
    
    /* PO */    
    #select_po
     
    /* GR */    
    #select_gr
    
    / * STOCK GR */    
    #select_stock_gr
    
    / * AP */        
    #select_stock_gr        

   / * Last AP */        
    #select_last_ap       
         
FROM nmt_procure_pr_row
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

    /* QO */    
    #join_qo
    
    /* PO */    
    #join_po
     
    /* GR */    
    #join_gr
    
    / * STOCK GR */    
    #join_stock_gr
    
    / * AP */        
    #join_stock_gr        

   / * Last AP */        
    #join_last_ap  
        
WHERE 1 %s
";

    const PR_ROW_SQL = "
SELECT
	nmt_procure_pr_row.*,
        
	nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,
    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,

    IFNULL(nmt_procure_qo_row.qo_qty,0) AS qo_qty,
    IFNULL(nmt_procure_qo_row.posted_qo_qty,0) AS posted_qo_qty,
    IFNULL(nmt_procure_qo_row.standard_qo_qty,0) AS standard_qo_qty,
    IFNULL(nmt_procure_qo_row.posted_standard_qo_qty,0) AS posted_standard_qo_qty,

    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
    IFNULL(nmt_procure_po_row.standard_po_qty,0) AS standard_po_qty,
    IFNULL(nmt_procure_po_row.posted_standard_po_qty,0) AS posted_standard_po_qty,
        
    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
    IFNULL(nmt_procure_gr_row.standard_gr_qty,0) AS standard_gr_qty,
    IFNULL(nmt_procure_gr_row.posted_standard_gr_qty,0) AS posted_standard_gr_qty,
        
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
    IFNULL(nmt_inventory_trx.standard_stock_gr_qty,0) AS standard_stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_standard_stock_gr_qty,0) AS posted_standard_stock_gr_qty,
       
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
    IFNULL(fin_vendor_invoice_row.standard_ap_qty,0) AS standard_ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_standard_ap_qty,0) AS posted_standard_ap_qty,
        
    last_ap.vendor_name as last_vendor_name,
    last_ap.unit_price as last_unit_price,
    last_ap.converted_standard_unit_price as last_standard_unit_price,
    last_ap.standard_convert_factor as last_standard_convert_factor,
    last_ap.currency_iso3 as last_currency_iso3
        
FROM nmt_procure_pr_row
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id

LEFT JOIN
(
%s   
)
AS nmt_procure_qo_row
ON nmt_procure_qo_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

LEFT JOIN
(
%s   
)
as last_ap
on last_ap.item_id = nmt_procure_pr_row.item_id

WHERE 1 %s
";

    const PR_ROW_SQL_1 = "
SELECT
	nmt_procure_pr_row.*,
        
	nmt_procure_pr.pr_name,
    nmt_procure_pr.created_on as pr_created_on,
    year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
        
    IFNULL(nmt_procure_qo_row.qo_qty,0) AS qo_qty,
    IFNULL(nmt_procure_qo_row.posted_qo_qty,0) AS posted_qo_qty,
    IFNULL(nmt_procure_qo_row.standard_qo_qty,0) AS standard_qo_qty,
    IFNULL(nmt_procure_qo_row.posted_standard_qo_qty,0) AS posted_standard_qo_qty,
        
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
    IFNULL(nmt_procure_po_row.standard_po_qty,0) AS standard_po_qty,
    IFNULL(nmt_procure_po_row.posted_standard_po_qty,0) AS posted_standard_po_qty,
        
    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
    IFNULL(nmt_procure_gr_row.standard_gr_qty,0) AS standard_gr_qty,
    IFNULL(nmt_procure_gr_row.posted_standard_gr_qty,0) AS posted_standard_gr_qty,
        
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
    IFNULL(nmt_inventory_trx.standard_stock_gr_qty,0) AS standard_stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_standard_stock_gr_qty,0) AS posted_standard_stock_gr_qty,
        
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
    IFNULL(fin_vendor_invoice_row.standard_ap_qty,0) AS standard_ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_standard_ap_qty,0) AS posted_standard_ap_qty
        
         
FROM nmt_procure_pr_row
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
        
LEFT JOIN
(
%s
)
AS nmt_procure_qo_row
ON nmt_procure_qo_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
%s
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
%s
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
%s
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
%s
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id

        
WHERE 1 %s
";

    const PR_QO_SQL = "
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
";

    const PR_PO_SQL = "
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
";

    const PR_POGR_SQL = "
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
";

    const PR_AP_SQL = "
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
";

    const PR_STOCK_GR_SQL = "
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
";

    const ITEM_LAST_AP_SQL = "
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
";
}
