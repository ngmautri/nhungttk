<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrQrSQL
{

    const PR_ROW_SQL = "
SELECT
	'PROCURE-PR' AS type,
     nmt_procure_pr.doc_type,
	nmt_procure_pr.id AS doc_id,
    nmt_procure_pr.submitted_on AS doc_date,
    nmt_procure_pr.pr_name AS doc_number,
    nmt_procure_pr.pr_auto_number AS sys_number,
    nmt_procure_pr_row.id AS row_id,
	nmt_procure_pr_row.item_id,
    nmt_inventory_item.item_name,
	nmt_procure_pr_row.quantity,
    nmt_procure_pr_row.standard_convert_factor,
    '' AS doc_unit,
    '' AS doc_unit_price,
    '' AS pr_row_id
FROM  nmt_procure_pr_row
LEFT JOIN nmt_procure_pr
ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
";

    const GR_ROW_SQL = "
'PROCURE-GR' AS type,
    nmt_procure_gr.doc_type,
	nmt_procure_gr.id AS doc_id,
    nmt_procure_gr.doc_date AS doc_date,
    nmt_procure_gr.doc_number AS doc_number,
    nmt_procure_gr.sys_number AS sys_number,
    nmt_procure_gr_row.id AS row_id,
	nmt_procure_gr_row.item_id,
    nmt_inventory_item.item_name,
	nmt_inventory_item.item_sku as item_sku,
    nmt_inventory_item.sys_number as item_sys,

	nmt_procure_gr_row.quantity,
    nmt_procure_gr_row.standard_convert_factor,
    nmt_procure_gr_row.doc_unit AS doc_unit,
    nmt_procure_gr_row.doc_unit_price AS doc_unit_price,
    nmt_procure_gr.vendor_id AS vendor_id,
    nmt_procure_gr.vendor_name AS vendor_name,
    nmt_procure_gr_row.pr_row_id AS pr_row_id
FROM  nmt_procure_gr_row
LEFT JOIN nmt_procure_gr
ON nmt_procure_gr.id = nmt_procure_gr_row.gr_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_gr_row.item_id
";

    const PO_AP_SQL = "
SELECT
	'PROCURE-AP' AS type,
    fin_vendor_invoice.doc_type,
    fin_vendor_invoice.vendor_id AS vendor_id,
    fin_vendor_invoice.vendor_name AS vendor_name,
	fin_vendor_invoice.id AS doc_id,
    fin_vendor_invoice.doc_date AS doc_date,
    fin_vendor_invoice.doc_number AS doc_number,
    fin_vendor_invoice.sys_number AS sys_number,
    fin_vendor_invoice.doc_status AS doc_status,

    fin_vendor_invoice_row.id AS row_id,
	fin_vendor_invoice_row.item_id,
    nmt_inventory_item.item_name,
	nmt_inventory_item.item_sku as item_sku,
    nmt_inventory_item.sys_number as item_sys,

	fin_vendor_invoice_row.quantity,
    fin_vendor_invoice_row.standard_convert_factor,
    fin_vendor_invoice_row.doc_unit AS doc_unit,
    fin_vendor_invoice_row.doc_unit_price AS doc_unit_price,
    fin_vendor_invoice_row.is_active AS row_is_active,
    fin_vendor_invoice_row.pr_row_id AS pr_row_id

FROM  fin_vendor_invoice_row
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id

union

SELECT
	'PROCURE-PO' AS type,
    nmt_procure_po.doc_type,
    nmt_procure_po.vendor_id AS vendor_id,
    nmt_procure_po.vendor_name AS vendor_name,
	nmt_procure_po.id AS doc_id,
    nmt_procure_po.doc_date AS doc_date,
    nmt_procure_po.doc_number AS doc_number,
    nmt_procure_po.sys_number AS sys_number,
    nmt_procure_po.doc_status AS doc_status,

    nmt_procure_po_row.id AS row_id,
	nmt_procure_po_row.item_id,
    nmt_inventory_item.item_name,
	nmt_inventory_item.item_sku as item_sku,
    nmt_inventory_item.sys_number as item_sys,

	nmt_procure_po_row.quantity,
    nmt_procure_po_row.standard_convert_factor,
    nmt_procure_po_row.doc_unit AS doc_unit,
    nmt_procure_po_row.doc_unit_price AS doc_unit_price,
    nmt_procure_po_row.is_active AS row_is_active,

    nmt_procure_po_row.pr_row_id AS pr_row_id

FROM  nmt_procure_po_row
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id";
}
