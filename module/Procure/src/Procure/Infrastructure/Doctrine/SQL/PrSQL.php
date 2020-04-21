<?php
namespace Procure\Infrastructure\Doctrine\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrSQL
{

    const PR_ROW_SQL = "
SELECT
	nmt_procure_pr.pr_name,
   nmt_procure_pr.created_on as pr_created_on,
        
   year(nmt_procure_pr.submitted_on) as pr_year,
    nmt_inventory_item.item_name,
        
	nmt_procure_pr_row.*,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
        
    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
        
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
        
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
        
    last_ap.vendor_name as vendor_name,
    last_ap.unit_price as unit_price,
    last_ap.currency_iso3 as currency_iso3
        
FROM nmt_procure_pr_row
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
        
LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row
        
	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
	GROUP BY nmt_procure_po_row.pr_row_id
        
)
AS nmt_procure_po_row
ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row
        
	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY fin_vendor_invoice_row.pr_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row
        
	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1
    GROUP BY nmt_procure_gr_row.pr_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
        
LEFT JOIN
(
	SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty
        
	FROM nmt_procure_pr_row
	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
        
        
left  join
(
	SELECT
	fin_vendor_invoice_row.item_id,
	fin_vendor_invoice_row.unit_price,
	fin_vendor_invoice.vendor_name,
	 fin_vendor_invoice.currency_iso3
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
        
WHERE 1 %s
";

    const PR_PO_ROW = "
   SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_draft = 1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS po_qty,
		SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN  nmt_procure_po_row.quantity ELSE 0 END) AS posted_po_qty
	FROM nmt_procure_pr_row
        
	JOIN nmt_procure_po_row
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1%s
	GROUP BY nmt_procure_po_row.pr_row_id
";

    const PR_AP_ROW = "
	SELECT
        nmt_procure_pr_row.id AS pr_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_draft =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty
	FROM nmt_procure_pr_row
        
	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1%s
    GROUP BY fin_vendor_invoice_row.pr_row_id
";

    const PR_GR_ROW = "
    SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_draft =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_pr_row
        
	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1%s
    GROUP BY nmt_procure_gr_row.pr_row_id
";

    const PR_STOCK_GR_ROW = "
   SELECT
		nmt_procure_pr_row.id AS pr_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted= 0 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
	    SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty
        
	FROM nmt_procure_pr_row
	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
    WHERE 1%s
	GROUP BY nmt_inventory_trx.pr_row_id
";

    const ITEM_LAST_AP_ROW = "
SELECT 
    fin_vendor_invoice_row.item_id,
    fin_vendor_invoice_row.unit_price,
    fin_vendor_invoice.vendor_name,
    fin_vendor_invoice.currency_iso3
FROM
    fin_vendor_invoice_row
        INNER JOIN
    (SELECT 
        MAX(fin_vendor_invoice_row.id) AS max_id,
            fin_vendor_invoice_row.item_id AS item_id
    FROM
        fin_vendor_invoice_row
    INNER JOIN fin_vendor_invoice ON fin_vendor_invoice_row.invoice_id = fin_vendor_invoice.id
    WHERE
        fin_vendor_invoice.doc_status = 'posted'
    GROUP BY fin_vendor_invoice_row.item_id) AS last_ap ON last_ap.max_id = fin_vendor_invoice_row.id
        AND fin_vendor_invoice_row.item_id = last_ap.item_id
        LEFT JOIN
    fin_vendor_invoice ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
";
}
