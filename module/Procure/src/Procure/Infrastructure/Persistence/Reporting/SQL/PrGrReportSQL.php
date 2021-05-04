<?php
namespace Procure\Infrastructure\Persistence\Reporting\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrGrReportSQL
{

    const REPORT_LIST = "

SELECT
		 nmt_procure_gr.company_id AS companyId,
		'PROCURE-GR' AS docTypeName,
		nmt_procure_gr.doc_type AS docType,
		nmt_procure_gr.vendor_id AS vendorId,
		nmt_procure_gr.vendor_name AS vendorName,
		nmt_procure_gr.id AS docId,
		nmt_procure_gr.doc_date AS docDate,
		nmt_procure_gr.doc_number AS docNumber,
		nmt_procure_gr.sys_number AS docSysNumber,
		nmt_procure_gr.doc_status AS docStatus,
		nmt_procure_gr.is_active AS docIsActive,
		nmt_procure_gr.posting_date AS docPostingDate,


		'' AS departmentId,
		nmt_procure_gr.warehouse_id AS warehouseId,
		nmt_inventory_warehouse.wh_code AS warehouseCode,
		nmt_inventory_warehouse.wh_name AS warehouseName,

		nmt_procure_gr_row.id AS rowId,
		nmt_procure_gr_row.row_identifer AS rowIdentifer,
		nmt_procure_gr_row.item_id AS itemId,
		nmt_inventory_item.item_name AS itemName,
		nmt_inventory_item.item_sku AS itemSku,
		nmt_inventory_item.sys_number AS itemSysNumber,
		nmt_inventory_item.standard_uom_id AS itemStandardUom,
		'NA' AS itemStandardUomName,

		nmt_procure_gr_row.doc_quantity AS  rowDocQuantity,
		nmt_procure_gr_row.standard_convert_factor AS rowStandardConvertFactor,
		nmt_procure_gr_row.doc_unit AS rowDocUnit,
	   nmt_procure_gr_row.doc_unit AS row_doc_unit_price,
		nmt_procure_gr_row.is_active AS rowIsActive,
		nmt_procure_gr_row.converted_standard_quantity AS convertedStandardQuantity,
	   nmt_procure_gr_row.converted_standard_unit_price AS convertedStandardUnitPrice,

		nmt_procure_gr_row.pr_row_id AS prRowId


	FROM  nmt_procure_gr_row
	LEFT JOIN nmt_procure_gr
	ON nmt_procure_gr.id = nmt_procure_gr_row.gr_id
	LEFT JOIN nmt_inventory_item
	ON nmt_inventory_item.id = nmt_procure_gr_row.item_id
	LEFT JOIN nmt_inventory_warehouse
	ON nmt_inventory_warehouse.id = nmt_procure_gr.warehouse_id
	WHERE 1 %s

UNION

	SELECT
		 nmt_procure_pr.company_id AS companyId,
		'PROCURE-PR' AS docTypeName,
		nmt_procure_pr.doc_type AS docType,
		'NA' AS vendorId,
		'NA' AS vendorName,
		nmt_procure_pr.id AS docId,
		nmt_procure_pr.doc_date AS docDate,
		nmt_procure_pr.doc_number AS docNumber,
		nmt_procure_pr.pr_auto_number AS docSysNumber,
		nmt_procure_pr.doc_status AS docStatus,
		nmt_procure_pr.is_active AS docIsActive,
        nmt_procure_pr.submitted_on AS docPostingDate,

		nmt_procure_pr.department_id AS departmentId,
		nmt_procure_pr.warehouse_id AS warehouseId,
		nmt_inventory_warehouse.wh_code AS warehouseCode,
		nmt_inventory_warehouse.wh_name AS warehouseName,

		nmt_procure_pr_row.id AS rowId,
		nmt_procure_pr_row.row_identifer AS rowIdentifer,
		nmt_procure_pr_row.item_id AS itemId,
		nmt_inventory_item.item_name AS itemName,
		nmt_inventory_item.item_sku AS itemSku,
		nmt_inventory_item.sys_number AS itemSysNumber,
		nmt_inventory_item.standard_uom_id AS itemStandardUom,
		'NA' AS itemStandardUomName,

		nmt_procure_pr_row.doc_quantity AS  rowDocQuantity,
		nmt_procure_pr_row.standard_convert_factor AS rowStandardConvertFactor,
		nmt_procure_pr_row.doc_unit AS rowDocUnit,
		'NA' AS row_doc_unit_price,
		nmt_procure_pr_row.is_active AS rowIsActive,
		nmt_procure_pr_row.converted_standard_quantity*-1 AS convertedStandardQuantity,
		'NA' AS convertedStandardUnitPrice,

		nmt_procure_pr_row.id AS prRowId


	FROM  nmt_procure_pr_row
	LEFT JOIN nmt_procure_pr
	ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
	LEFT JOIN nmt_inventory_item
	ON nmt_inventory_item.id = nmt_procure_pr_row.item_id
	LEFT JOIN nmt_inventory_warehouse
	ON nmt_inventory_warehouse.id = nmt_procure_pr.warehouse_id

	WHERE 1 %s







";
}