<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSQL
{

    const PO_LIST = "
SELECT
	nmt_procure_po.*,
	COUNT(nmt_procure_po_row.id) AS active_row,
    COUNT(nmt_procure_po_row.id) AS total_row,
	MAX(nmt_procure_po_row.row_number) AS max_row_number,
  	SUM(nmt_procure_po_row.net_amount) AS net_amount,
    SUM(nmt_procure_po_row.tax_amount) AS tax_amount,
	SUM(nmt_procure_po_row.gross_amount) AS gross_amount,
	SUM(nmt_procure_po_row.billed_amount) AS billed_amount,
	SUM(nmt_procure_po_row.billed_amount) AS billed_qty,
FROM nmt_procure_po
        
LEFT JOIN
(
       SELECT
		fin_vendor_invoice_row.id AS invoice_row_id,
        nmt_procure_po_row.*,
        SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS billed_amount,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.quantity) ELSE 0 END) AS billed_qty
    FROM nmt_procure_po_row
    LEFT JOIN fin_vendor_invoice_row
	ON nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id
    WHERE nmt_procure_po_row.is_active =1
   GROUP BY nmt_procure_po_row.id
        
)AS
nmt_procure_po_row
ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
WHERE 1
";

    const PO_ROW_ALL = "
    SELECT
    nmt_procure_po_row.po_id AS po_id,
    nmt_procure_po_row.id AS po_row_id,
   	IFNULL(nmt_procure_po_row.quantity,0) AS po_qty,
	IFNULL(fin_vendor_invoice_row.posted_ap_qty,0) AS posted_ap_qty,
    IFNULL(nmt_procure_gr_row.posted_gr_qty,0) AS posted_gr_qty,
    IFNULL(fin_vendor_invoice_row.billed_amount,0) AS billed_amount,
    SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN (nmt_procure_po_row.net_amount) ELSE 0 END) AS net_amount,
    SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN (nmt_procure_po_row.tax_amount) ELSE 0 END) AS tax_amount,
    SUM(CASE WHEN nmt_procure_po_row.is_active =1 AND  nmt_procure_po_row.is_posted =1 THEN (nmt_procure_po_row.gross_amount) ELSE 0 END) AS gross_amount
FROM nmt_procure_po_row
LEFT JOIN
(
    SELECT
        nmt_procure_po_row.id AS po_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_draft =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty,
        SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS billed_amount,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN (fin_vendor_invoice_row.quantity) ELSE 0 END) AS billed_qty        
	FROM nmt_procure_po_row
        
	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
	WHERE fin_vendor_invoice_row.is_active=1
    GROUP BY fin_vendor_invoice_row.po_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id

LEFT JOIN
(
    SELECT
		nmt_procure_po_row.id AS po_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_draft =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty
	FROM nmt_procure_po_row
        
	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
	WHERE nmt_procure_gr_row.is_active=1
    GROUP BY nmt_procure_gr_row.po_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
WHERE nmt_procure_po_row.is_active=1
group by nmt_procure_po_row.id
  ";
}
