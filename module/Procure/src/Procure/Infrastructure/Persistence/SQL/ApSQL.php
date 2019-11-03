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
    
 }
