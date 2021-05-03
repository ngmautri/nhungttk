<?php
namespace Procure\Infrastructure\Persistence\Reporting\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoApReportSQL
{

    const REPORT_LIST = "
SELECT
     fin_vendor_invoice.company_id as company_id,
	'PROCURE-AP' doc_type_name,
    fin_vendor_invoice.doc_type,
    fin_vendor_invoice.vendor_id AS vendor_id,
    fin_vendor_invoice.vendor_name AS vendor_name,
	fin_vendor_invoice.id AS doc_id,
    fin_vendor_invoice.doc_date AS doc_date,
    fin_vendor_invoice.posting_date AS doc_posting_date,
    fin_vendor_invoice.doc_number AS doc_number,
    fin_vendor_invoice.sys_number AS doc_sys_number,
    fin_vendor_invoice.doc_status AS doc_status,
    fin_vendor_invoice.is_active AS doc_is_active,

    fin_vendor_invoice_row.id AS row_id,
     fin_vendor_invoice_row.row_identifer AS row_identifer,
	fin_vendor_invoice_row.item_id AS item_id,
    nmt_inventory_item.item_name AS item_name,
	nmt_inventory_item.item_sku AS item_sku,
    nmt_inventory_item.sys_number AS item_sys_number,

	fin_vendor_invoice_row.doc_quantity AS row_doc_quantity,
    fin_vendor_invoice_row.standard_convert_factor AS row_standard_convert_factor,
    fin_vendor_invoice_row.doc_unit AS row_doc_unit,
    fin_vendor_invoice_row.doc_unit_price AS row_doc_unit_price,
    fin_vendor_invoice_row.is_active AS row_is_active,
    fin_vendor_invoice_row.converted_standard_quantity AS converted_standard_quantity,
    fin_vendor_invoice_row.converted_standard_unit_price AS converted_standard_unit_price,


    fin_vendor_invoice_row.pr_row_id AS pr_row_id,
	fin_vendor_invoice_row.po_row_id AS po_row_id

FROM  fin_vendor_invoice_row
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id
WHERE 1 %s

UNION

SELECT
     nmt_procure_po.company_id as company_id,
	'PROCURE-PO' AS type,
    nmt_procure_po.doc_type,
    nmt_procure_po.vendor_id AS vendor_id,
    nmt_procure_po.vendor_name AS vendor_name,
	nmt_procure_po.id AS doc_id,
    nmt_procure_po.doc_date AS doc_date,
    nmt_procure_po.doc_date AS doc_posting_date,
    nmt_procure_po.doc_number AS doc_number,
    nmt_procure_po.sys_number AS doc_sys_number,
    nmt_procure_po.doc_status AS doc_status,
	nmt_procure_po.is_active AS doc_is_active,

    nmt_procure_po_row.id AS row_id,
    nmt_procure_po_row.row_identifer AS row_identifer,
	nmt_procure_po_row.item_id AS item_id,
    nmt_inventory_item.item_name AS item_name,
	nmt_inventory_item.item_sku AS item_sku,
    nmt_inventory_item.sys_number AS item_sys_number,

	nmt_procure_po_row.doc_quantity AS  row_doc_quantity,
    nmt_procure_po_row.standard_convert_factor AS row_standard_convert_factor,
    nmt_procure_po_row.doc_unit AS row_doc_unit,
    nmt_procure_po_row.doc_unit_price AS row_doc_unit_price,
    nmt_procure_po_row.is_active AS row_is_active,
    nmt_procure_po_row.converted_standard_quantity AS converted_standard_quantity,
    nmt_procure_po_row.converted_standard_unit_price AS converted_standard_unit_price,


    nmt_procure_po_row.pr_row_id AS pr_row_id,
    nmt_procure_po_row.id AS po_row_id

FROM  nmt_procure_po_row
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id
WHERE 1 %s

";
}