<?php
namespace Procure\Infrastructure\Doctrine\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSQL
{

    const SQL_ROW_PO_AP = "
SELECT
    nmt_procure_po_row.id AS po_row_id,
    nmt_procure_po.vendor_id as vendor_id,
    year(nmt_procure_po.contract_date) as po_year,
    nmt_procure_po.contract_no as po_number,
    fin_vendor_invoice_row.invoice_id as ap_id,
	IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS draft_ap_qty,
    IFNULL(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS posted_ap_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END),0) AS confirmed_ap_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END)-SUM(CASE WHEN fin_vendor_invoice_row.is_draft=0 AND fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS open_ap_qty,
    ifnull(SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END),0)AS billed_amount

FROM nmt_procure_po_row

left join nmt_procure_po
on nmt_procure_po.id = nmt_procure_po_row.po_id

LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id =  nmt_procure_po_row.id

WHERE 1%s
GROUP BY nmt_procure_po_row.id
    ";

    const SQL_ROW_PO_GR = "
SELECT

	IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS draft_gr_qty,
    IFNULL(SUM(CASE WHEN nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS posted_gr_qty,
    IFNULL(nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END),0) AS confirmed_gr_balance,
    nmt_procure_po_row.quantity-SUM(CASE WHEN nmt_procure_gr_row.is_draft=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END)-SUM(CASE WHEN nmt_procure_gr_row.is_draft=0 AND nmt_procure_gr_row.is_posted=1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS open_gr_qty,
    nmt_procure_po_row.id as po_row_id,
    nmt_procure_po.vendor_id as vendor_id,
    year(nmt_procure_po.contract_date) as po_year,
    nmt_procure_gr_row.gr_id as gr_id,
    nmt_procure_po.contract_no as po_number


FROM nmt_procure_po_row

left join nmt_procure_po
on nmt_procure_po.id = nmt_procure_po_row.po_id

LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id =  nmt_procure_po_row.id

WHERE 1%s
GROUP BY nmt_procure_po_row.id
    ";
}
