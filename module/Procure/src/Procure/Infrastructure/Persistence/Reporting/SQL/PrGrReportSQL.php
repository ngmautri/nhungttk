<?php
namespace Procure\Infrastructure\Persistence\Reporting\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrGrReportSQL
{

    const QR_LIST = "
SELECT
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
}