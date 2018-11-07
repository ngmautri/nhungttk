<?php
namespace Application\Repository\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NmtProcurePoRepositorySQL
{
    
        const PR_ROW_SQL = "
SELECT
	nmt_procure_pr_row.*,
	IFNULL(nmt_procure_pr_row.quantity,0) AS pr_qty,
    IFNULL(nmt_procure_po_row.po_qty,0) AS po_qty,
    IFNULL(nmt_procure_po_row.posted_po_qty,0) AS posted_po_qty,
    
    IFNULL(nmt_procure_gr_row.gr_qty,0) AS gr_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
    
    IFNULL(nmt_inventory_trx.stock_gr_qty,0) AS stock_gr_qty,
    IFNULL(nmt_inventory_trx.posted_stock_gr_qty,0) AS posted_stock_gr_qty,
 
    IFNULL(fin_vendor_invoice_row.ap_qty,0) AS ap_qty,
    IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty
    
FROM nmt_procure_pr_row

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
	WHERE 1 %s
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
	WHERE 1 %s 
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
	WHERE 1 %s 
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
    WHERE 1 %s
	GROUP BY nmt_inventory_trx.pr_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.pr_row_id = nmt_procure_pr_row.id
WHERE 1 %s
ORDER By nmt_procure_pr_row.created_on DESC
";

        const ITEM_PRICE_SQL = '
SELECT
	"QO" AS doc_type ,
    
    nmt_inventory_item.id AS item_id,
    nmt_inventory_item.item_name AS item_name,
    nmt_inventory_item.item_sku,
    nmt_inventory_item.item_sku1,
    nmt_inventory_item.item_sku,
    
    nmt_procure_qo.id AS source_id,
	nmt_procure_qo.vendor_id,
    nmt_procure_qo.vendor_name,
    nmt_procure_qo.currency_id,
    nmt_procure_qo.doc_currency_id,
    nmt_procure_qo.exchange_rate,
    
    nmt_procure_qo_row.quantity,
    nmt_procure_qo_row.conversion_factor,
    nmt_procure_qo_row.unit AS doc_unit,
    nmt_procure_qo_row.unit_price,
    nmt_procure_qo_row.unit_price*nmt_procure_qo.exchange_rate AS lc_unit_price,
       nmt_procure_qo_row.unit_price*nmt_procure_qo.exchange_rate* nmt_procure_qo_row.quantity AS lc_total_price

FROM nmt_procure_qo_row
            
LEFT JOIN nmt_procure_qo
ON nmt_procure_qo.id = nmt_procure_qo_row.qo_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_qo_row.item_id
WHERE 1 AND nmt_procure_qo_row.item_id=%s

UNION

SELECT
	"AP" AS doc_type ,
    nmt_inventory_item.id AS item_id,
    nmt_inventory_item.item_name AS item_name,
    nmt_inventory_item.item_sku,
    nmt_inventory_item.item_sku1,
    nmt_inventory_item.item_sku,
    
    
    fin_vendor_invoice.id AS source_id,
	fin_vendor_invoice.vendor_id,
    fin_vendor_invoice.vendor_name,
    fin_vendor_invoice.doc_currency_id,
    fin_vendor_invoice.currency_id,
    fin_vendor_invoice.exchange_rate,   

    fin_vendor_invoice_row.quantity,
    fin_vendor_invoice_row.conversion_factor,
    fin_vendor_invoice_row.unit AS doc_unit,
    fin_vendor_invoice_row.unit_price,
    fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate AS lc_unit_price,
    fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate*fin_vendor_invoice_row.quantity AS lc_total_price

FROM fin_vendor_invoice_row
            
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
            
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id
WHERE 1 AND fin_vendor_invoice_row.item_id=%s

';

}

