<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApSQL
{

    const AP_ROW_SQL = "

SELECT

fin_vendor_invoice_row.*,

fin_vendor_invoice.id as invoice_id,
fin_vendor_invoice.vendor_id,
fin_vendor_invoice.sap_doc,
fin_vendor_invoice.vendor_name,
fin_vendor_invoice.token AS invoice_token,

YEAR(fin_vendor_invoice.posting_date) AS posting_year,
MONTH(fin_vendor_invoice.posting_date) AS posting_month,

fin_vendor_invoice.posting_date,
fin_vendor_invoice.contract_date,
fin_vendor_invoice.doc_status AS ap_doc_status,

fin_vendor_invoice.doc_currency_id,
fin_vendor_invoice.currency_id,
fin_vendor_invoice.currency_iso3,
fin_vendor_invoice.local_currency_id,
fin_vendor_invoice.exchange_rate,

nmt_inventory_item.token AS item_token,
nmt_inventory_item.sys_number as item_sys_number,
nmt_inventory_item.item_name,
nmt_inventory_item.item_sku,
nmt_inventory_item.item_sku1,
nmt_inventory_item.item_sku2,

nmt_procure_pr.id AS pr_id,
nmt_procure_pr.token AS pr_token,
nmt_procure_pr.pr_number,
nmt_procure_pr.pr_auto_number,
nmt_procure_pr.submitted_on AS pr_date,

nmt_procure_po.id AS po_id,
nmt_procure_po.token AS po_token,
nmt_procure_po.contract_no,
nmt_procure_po.sys_number as po_sys_number,
nmt_procure_po.contract_date AS po_date

FROM fin_vendor_invoice_row

LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id

LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id

LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr_row.id = fin_vendor_invoice_row.pr_row_id

LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id

LEFT JOIN nmt_procure_po_row
ON nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id

LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id

WHERE 1  
";

    const AP_ROW_TOTAL_SQL = "
        
SELECT

count(fin_vendor_invoice_row.id) as total_rows        
        
FROM fin_vendor_invoice_row
        
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id
        
LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr_row.id = fin_vendor_invoice_row.pr_row_id
        
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
        
LEFT JOIN nmt_procure_po_row
ON nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id
        
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
WHERE 1
";

    const AP_LIST = "
SELECT
	fin_vendor_invoice.*,
    COUNT(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.id) ELSE NULL END) AS total_row,
    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) AS gross_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.discount_rate*fin_vendor_invoice_row.gross_amount) ELSE 0 END) AS gross_discount_amount   
FROM fin_vendor_invoice        
LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id        
WHERE 1
";

    const AP_LIST_DETAILS = "
SELECT
	fin_vendor_invoice.*,
    pmt_outgoing.total_doc_amount as total_doc_amount_paid, 
    pmt_outgoing.total_local_amount as total_local_amount_paid,
    pmt_outgoing.doc_currency_id as doc_currency_paid,
    pmt_outgoing.exchange_rate as exchange_rate_paid,
    
	COUNT(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.id) ELSE NULL END) AS active_row,
    ifnull(MAX(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.row_number) ELSE null END),0) AS max_row_number,
	COUNT(fin_vendor_invoice_row.id) AS total_row,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.gross_amount) ELSE 0 END) AS gross_amount,
    
    ifnull(nmt_application_attachment.total_attachment,0) as total_attachment,
    ifnull(nmt_application_attachment.total_picture,0) as total_picture
  
FROM fin_vendor_invoice

LEFT JOIN fin_vendor_invoice_row
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id

LEFT JOIN
(	
SELECT
	fin_vendor_invoice.id as v_invoice_id,
	COUNT(nmt_application_attachment.id) AS total_attachment,
   	COUNT(CASE WHEN nmt_application_attachment.is_picture =1 THEN (nmt_application_attachment.id) ELSE NULL END) AS total_picture
    FROM fin_vendor_invoice
    INNer JOIN nmt_application_attachment
    ON fin_vendor_invoice.id = nmt_application_attachment.v_invoice_id
    group by fin_vendor_invoice.id
)
AS nmt_application_attachment
ON fin_vendor_invoice.id = nmt_application_attachment.v_invoice_id

LEFT JOIN
(
	SELECT
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'D' AND pmt_outgoing.is_active = 1) THEN (pmt_outgoing.doc_amount) ELSE 0 END) AS debit_doc_amount,
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'C' and pmt_outgoing.is_active = 1) THEN (pmt_outgoing.doc_amount) ELSE 0 END) AS credit_doc_amount,

		SUM(CASE WHEN (pmt_outgoing.posting_key = 'D' AND pmt_outgoing.is_active = 1) THEN (pmt_outgoing.doc_amount) ELSE 0 END) -
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'C' and pmt_outgoing.is_active = 1) THEN (pmt_outgoing.doc_amount) ELSE 0 END) AS total_doc_amount,
        
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'D' AND pmt_outgoing.is_active = 1) THEN (pmt_outgoing.local_amount) ELSE 0 END) AS debit_local_amount,
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'C' and pmt_outgoing.is_active = 1) THEN (pmt_outgoing.local_amount) ELSE 0 END) AS credit_local_amount,

		SUM(CASE WHEN (pmt_outgoing.posting_key = 'D' AND pmt_outgoing.is_active = 1) THEN (pmt_outgoing.local_amount) ELSE 0 END) -
		SUM(CASE WHEN (pmt_outgoing.posting_key = 'C' and pmt_outgoing.is_active = 1) THEN (pmt_outgoing.local_amount) ELSE 0 END) AS total_local_amount,
		pmt_outgoing.doc_currency_id,
		pmt_outgoing.exchange_rate,
		
		fin_vendor_invoice.id as ap_id
		
	FROM fin_vendor_invoice


	left join pmt_outgoing
	on pmt_outgoing.ap_invoice_id = fin_vendor_invoice.id

	GROUP BY fin_vendor_invoice.id
)
AS pmt_outgoing
ON fin_vendor_invoice.id = pmt_outgoing.ap_id

where 1
";
}
