<?php
namespace Inventory\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportSQL
{

    const ITEM_PRICE_COMPARE_SQL = '
        
SELECT
        
*        
FROM
        
(
        
SELECT
	"QO" AS doc_type ,
    nmt_procure_qo_row.row_identifer,
        
    nmt_inventory_item.id AS item_id,
    nmt_inventory_item.item_name AS item_name,
    nmt_inventory_item.item_sku,
    nmt_inventory_item.item_sku1,
    nmt_inventory_item.item_sku2,
        
        
	nmt_procure_qo.id AS source_id,
    nmt_procure_qo.vendor_name,
        
	nmt_procure_qo.currency_id,
	nmt_application_currency.currency,
        
	nmt_procure_qo.doc_currency_id,
	nmt_application_currency_1.currency AS doc_currency,
        
    nmt_procure_qo.local_currency_id,
	nmt_application_currency_2.currency AS local_currency,
        
    nmt_procure_qo.exchange_rate,
	nmt_procure_qo.is_active AS source_is_active,
    nmt_procure_qo.created_on AS source_created_on,
        
    nmt_procure_qo_row.quantity,
    nmt_procure_qo_row.conversion_factor,
    nmt_procure_qo_row.unit AS doc_unit,
    nmt_procure_qo_row.unit_price,
    nmt_procure_qo_row.unit_price*nmt_procure_qo.exchange_rate AS lc_unit_price,
    nmt_procure_qo_row.unit_price*nmt_procure_qo.exchange_rate* nmt_procure_qo_row.quantity AS lc_total_price,
    nmt_application_incoterms.incoterm,
    nmt_procure_qo.incoterm_place,
    nmt_procure_qo_row.vendor_item_code AS vendor_item_code,
    nmt_procure_qo_row.vendor_item_name AS vendor_item_name
        
FROM nmt_procure_qo_row
        
LEFT JOIN nmt_procure_qo
ON nmt_procure_qo.id = nmt_procure_qo_row.qo_id
        
LEFT JOIN nmt_application_incoterms
ON nmt_application_incoterms.id = nmt_procure_qo.incoterm_id
        
        
LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = nmt_procure_qo.currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_1
ON nmt_application_currency_1.id = nmt_procure_qo.doc_currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_2
ON nmt_application_currency_2.id = nmt_procure_qo.local_currency_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_qo_row.item_id
WHERE 1 AND nmt_procure_qo_row.item_id=%s
        
UNION
        
SELECT
	"PO" AS doc_type ,
    nmt_procure_po_row.row_identifer,
        
    nmt_inventory_item.id AS item_id,
    nmt_inventory_item.item_name AS item_name,
    nmt_inventory_item.item_sku,
    nmt_inventory_item.item_sku1,
    nmt_inventory_item.item_sku,
        
	nmt_procure_po.id AS source_id,
    nmt_procure_po.vendor_name,
	nmt_procure_po.currency_id,
	nmt_application_currency.currency,
        
	nmt_procure_po.doc_currency_id,
	nmt_application_currency_1.currency AS doc_currency,
        
    nmt_procure_po.local_currency_id,
	nmt_application_currency_2.currency AS local_currency,
        
    nmt_procure_po.exchange_rate,
	nmt_procure_po.is_active AS source_is_active,
    nmt_procure_po.created_on AS source_created_on,
        
    nmt_procure_po_row.quantity,
    nmt_procure_po_row.conversion_factor,
    nmt_procure_po_row.unit AS doc_unit,
    nmt_procure_po_row.unit_price,
    nmt_procure_po_row.unit_price*nmt_procure_po.exchange_rate AS lc_unit_price,
	nmt_procure_po_row.unit_price*nmt_procure_po.exchange_rate* nmt_procure_po_row.quantity AS lc_total_price,
    nmt_application_incoterms.incoterm,
    nmt_procure_po.incoterm_place,
    nmt_procure_po_row.vendor_item_code AS vendor_item_code,
    nmt_procure_po_row.vendor_item_name AS vendor_item_name
        
FROM nmt_procure_po_row
        
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
LEFT JOIN nmt_application_incoterms
ON nmt_application_incoterms.id = nmt_procure_po.incoterm_id
        
        
LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = nmt_procure_po.currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_1
ON nmt_application_currency_1.id = nmt_procure_po.doc_currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_2
ON nmt_application_currency_2.id = nmt_procure_po.local_currency_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id
WHERE 1 AND nmt_procure_po_row.item_id=%s
        
UNION
        
SELECT
	"AP" AS doc_type ,
    fin_vendor_invoice_row.row_identifer,
        
    nmt_inventory_item.id AS item_id,
    nmt_inventory_item.item_name AS item_name,
    nmt_inventory_item.item_sku,
    nmt_inventory_item.item_sku1,
    nmt_inventory_item.item_sku,
        
        
    fin_vendor_invoice.id AS source_id,
    fin_vendor_invoice.vendor_name,
        
	fin_vendor_invoice.currency_id,
	nmt_application_currency.currency,
        
	fin_vendor_invoice.doc_currency_id,
	nmt_application_currency_1.currency AS doc_currency,
        
    fin_vendor_invoice.local_currency_id,
	nmt_application_currency_2.currency AS local_currency,
        
    fin_vendor_invoice.exchange_rate,
	fin_vendor_invoice.is_active AS source_is_active,
    fin_vendor_invoice.created_on AS source_created_on,
        
    fin_vendor_invoice_row.quantity,
    fin_vendor_invoice_row.conversion_factor,
    fin_vendor_invoice_row.unit AS doc_unit,
    fin_vendor_invoice_row.unit_price,
    fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate AS lc_unit_price,
    fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate*fin_vendor_invoice_row.quantity AS lc_total_price,
    nmt_application_incoterms.incoterm,
    fin_vendor_invoice.incoterm_place,
    fin_vendor_invoice_row.vendor_item_code AS vendor_item_code,
    fin_vendor_invoice_row.vendor_item_name AS vendor_item_name

        
        
FROM fin_vendor_invoice_row
        
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
        
LEFT JOIN nmt_application_incoterms
ON nmt_application_incoterms.id = fin_vendor_invoice.incoterm_id
        
        
LEFT JOIN nmt_application_currency
ON nmt_application_currency.id = fin_vendor_invoice.currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_1
ON nmt_application_currency_1.id = fin_vendor_invoice.doc_currency_id
        
LEFT JOIN nmt_application_currency AS nmt_application_currency_2
ON nmt_application_currency_2.id = fin_vendor_invoice.local_currency_id
        
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = fin_vendor_invoice_row.item_id
WHERE 1 AND fin_vendor_invoice_row.item_id=%s
        
)
        
AS item_price_tbl
        
ORDER BY item_price_tbl.lc_unit_price ASC
        
';
}
