<?php
namespace Inventory\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialSQL
{

    const SELECT_GR_KEY = "/*select_gr*/";

    const SELECT_AP_KEY = "/*select_ap*/";

    const JOIN_GR_KEY = "/*join_gr*/";

    const JOIN_AP_KEY = "/*join_ap*/";

    const SERIAL_LIST_TEMPLATE = "
SELECT

	/*FIXED PART */
	nmt_inventory_item_serial.*,
	 
	/*Select GR*/    
	/*select_gr*/
   
	/*Select AP*/    
	/*select_ap*/    

    /*FIXED PART */
    nmt_inventory_item.item_name as item_serial_name,
	nmt_inventory_item.token as item_token	

FROM nmt_inventory_item_serial
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_inventory_item_serial.item_id

     
/*Join GR */    
/*join_gr*/    
    
/*Join AP */        
/*join_ap*/    
        
WHERE 1 %s
";

    const SERIAL_AP_SQL = "
LEFT JOIN (
	SELECT
		fin_vendor_invoice.vendor_name,
		fin_vendor_invoice.vendor_id AS vendor_id,
		fin_vendor_invoice.token AS invoice_token,
		fin_vendor_invoice.sys_number AS invoice_sys_number,
		fin_vendor_invoice.currency_iso3 AS invoice_doc_currency,
		fin_vendor_invoice.posting_date AS invoice_posting_date,
		fin_vendor_invoice_row.*
	FROM fin_vendor_invoice_row
   LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
	Where 1 %s
) as fin_vendor_invoice_row
ON fin_vendor_invoice_row.id = nmt_inventory_item_serial.ap_row_id        
";

    const SERIAL_LIST = "
SELECT
	nmt_inventory_item_serial.*,
	nmt_procure_gr_row.gr_id,
    fin_vendor_invoice_row.vendor_id,
    fin_vendor_invoice_row.vendor_name,
    fin_vendor_invoice_row.invoice_id,
	fin_vendor_invoice_row.invoice_token,
    fin_vendor_invoice_row.invoice_sys_number,
    fin_vendor_invoice_row.invoice_doc_currency,
	fin_vendor_invoice_row.invoice_posting_date,
	nmt_inventory_item.item_name,
	nmt_inventory_item.id as item_id,
    nmt_inventory_item.token as item_token	
FROM nmt_inventory_item_serial
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_inventory_item_serial.item_id
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr_row.id = nmt_inventory_item_serial.gr_row_id

LEFT JOIN (
	SELECT
		fin_vendor_invoice.vendor_name,
		fin_vendor_invoice.vendor_id AS vendor_id,
		fin_vendor_invoice.token AS invoice_token,
		fin_vendor_invoice.sys_number AS invoice_sys_number,
		fin_vendor_invoice.currency_iso3 AS invoice_doc_currency,
		fin_vendor_invoice.posting_date AS invoice_posting_date,
		fin_vendor_invoice_row.*
	FROM fin_vendor_invoice_row
   LEFT JOIN fin_vendor_invoice
	ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
	Where 1 %s
) as fin_vendor_invoice_row
ON fin_vendor_invoice_row.id = nmt_inventory_item_serial.ap_row_id
WHERE 

1 %s
";
}
