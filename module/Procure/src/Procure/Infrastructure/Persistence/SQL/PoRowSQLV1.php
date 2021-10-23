<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
final class PoRowSQLV1
{

    const SELECT_QO_KEY = "/*select_qo*/";

    const SELECT_GR_KEY = "/*select_gr*/";

    const SELECT_STOCK_GR_KEY = "/*select_stock_gr*/";

    const SELECT_STOCK_RETURN_KEY = "/*select_stock_return*/";

    const SELECT_AP_KEY = "/*select_ap*/";

    const JOIN_QO_KEY = "/*join_qo*/";

    const JOIN_GR_KEY = "/*join_gr*/";

    const JOIN_STOCK_GR_KEY = "/*join_stock_gr*/";

    const JOIN_STOCK_RETURN_KEY = "/*join_stock_return*/";

    const JOIN_AP_KEY = "/*join_ap*/";

    const PO_ROW_SQL_TEMPLATE = "
SELECT

	/*FIXED PART */
	nmt_procure_po_row.*,
	
	/*Select QO*/    
	/*select_qo*/
     
	/*Select GR*/    
	/*select_gr*/
   
	/*Select Stock GR */    
	/*select_stock_gr*/

	/*Select Stock RETURN */    
	/*select_stock_return*/
    
	/*Select AP */        
	/*select_ap*/      
    
	nmt_procure_po.doc_number as po_number,
    nmt_procure_po.doc_date as po_date,
	nmt_procure_po.created_on AS po_created_on,
	YEAR(nmt_procure_po.doc_date) AS po_year,
	nmt_inventory_item.item_name,
	IFNULL(nmt_procure_po_row.doc_quantity,0) AS po_qty,  
	IFNULL(nmt_procure_po_row.converted_standard_quantity,0) AS standard_po_qty
  
FROM nmt_procure_po_row
       
LEFT JOIN nmt_procure_po
ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
LEFT JOIN nmt_inventory_item
ON nmt_inventory_item.id = nmt_procure_po_row.item_id

/*Join QO */    
/*join_qo*/    
     
/*Join GR */    
/*join_gr*/    
    
/*Join STOCK GR */    
/*join_stock_gr*/   

/*Join STOCK RETURN */    
/*join_stock_return*/   
    
/*Join AP */        
/*join_ap*/    
        
WHERE 1 %s
";

    const PO_POGR_SQL = "
LEFT JOIN
(
        
 SELECT
		nmt_procure_po_row.id AS po_row_id,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.quantity ELSE 0 END) AS posted_gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN  nmt_procure_gr_row.converted_standard_quantity ELSE 0 END) AS standard_gr_qty,
		SUM(CASE WHEN nmt_procure_gr_row.is_active =1 AND  nmt_procure_gr_row.is_posted =1 THEN  nmt_procure_gr_row.converted_standard_quantity ELSE 0 END) AS posted_standard_gr_qty
	FROM nmt_procure_po_row
        
    LEFT JOIN nmt_procure_po
    ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_po_row.item_id
        
        
	JOIN nmt_procure_gr_row
	ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
	WHERE 1 %s
	GROUP BY nmt_procure_gr_row.po_row_id
)
AS nmt_procure_gr_row
ON nmt_procure_gr_row.po_row_id = nmt_procure_po_row.id
";

    const PO_AP_SQL = "
LEFT JOIN
(
  SELECT
        nmt_procure_po_row.id AS po_row_id,
 	    SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_draft =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS ap_qty,
    	SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.quantity ELSE 0 END) AS posted_ap_qty,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN  fin_vendor_invoice_row.converted_standard_quantity ELSE 0 END) AS standard_ap_qty,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 AND  fin_vendor_invoice_row.is_posted =1 THEN  fin_vendor_invoice_row.converted_standard_quantity ELSE 0 END) AS posted_standard_ap_qty,
        SUM(CASE WHEN fin_vendor_invoice_row.is_posted=1 THEN  fin_vendor_invoice_row.net_amount ELSE 0 END) AS billed_amount

	
 FROM nmt_procure_po_row
    
    LEFT JOIN nmt_procure_po
    ON nmt_procure_po.id = nmt_procure_po_row.po_id

    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_po_row.item_id
        
	JOIN fin_vendor_invoice_row
	ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
	WHERE 1 %s
    GROUP BY fin_vendor_invoice_row.po_row_id
)
AS fin_vendor_invoice_row
ON fin_vendor_invoice_row.po_row_id = nmt_procure_po_row.id
";

    const PO_STOCK_GR_SQL = "
LEFT JOIN
(
  SELECT
		nmt_procure_po_row.id AS po_row_id,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND  nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.quantity ELSE 0 END) AS posted_stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 THEN  nmt_inventory_trx.converted_standard_quantity ELSE 0 END) AS standard_stock_gr_qty,
		SUM(CASE WHEN nmt_inventory_trx.is_active =1 AND  nmt_inventory_trx.is_posted =1 THEN  nmt_inventory_trx.converted_standard_quantity ELSE 0 END) AS posted_standard_stock_gr_qty
	FROM nmt_procure_po_row

    LEFT JOIN nmt_procure_po
    ON nmt_procure_po.id = nmt_procure_po_row.po_id

    LEFT JOIN nmt_inventory_item
    ON nmt_inventory_item.id = nmt_procure_po_row.item_id

	JOIN nmt_inventory_trx
	ON nmt_inventory_trx.po_row_id = nmt_procure_po_row.id
	WHERE 1
	GROUP BY nmt_inventory_trx.po_row_id
)
AS nmt_inventory_trx
ON nmt_inventory_trx.po_row_id = nmt_procure_po_row.id   
";
}
