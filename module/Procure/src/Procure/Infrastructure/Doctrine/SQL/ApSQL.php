<?php
namespace Procure\Infrastructure\Doctrine\SQL;

/**
 *
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApSQL
{

    const SQL_ROW_AP_GR = "
SELECT
    nmt_procure_gr_row.id AS gr_row_id,
    nmt_procure_gr.vendor_id as vendor_id,
    year(nmt_procure_gr.gr_date) as gr_year,
    nmt_procure_gr.sys_number as gr_number, 
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_gr_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_gr_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount
            
FROM nmt_procure_gr_row

left join nmt_procure_gr
on nmt_procure_gr.id = nmt_procure_gr_row.gr_id
            
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.gr_row_id =  nmt_procure_gr_row.id
            
WHERE 1%s
GROUP BY nmt_procure_gr_row.id
    ";

    const SQL_ROW_GR_PO = "
    ";
}
