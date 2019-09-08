<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSQL
{

    const PO_LIST = "
SELECT
	nmt_procure_po.*,
	COUNT(nmt_procure_po_row.id) AS active_row,
    COUNT(nmt_procure_po_row.id) AS total_row,
	MAX(nmt_procure_po_row.row_number) AS max_row_number,
  	SUM(nmt_procure_po_row.net_amount) AS net_amount,
    SUM(nmt_procure_po_row.tax_amount) AS tax_amount,
	SUM(nmt_procure_po_row.gross_amount) AS gross_amount,
	SUM(nmt_procure_po_row.billed_amount) AS billed_amount
FROM nmt_procure_po
        
LEFT JOIN
(
       SELECT
		fin_vendor_invoice_row.id AS invoice_row_id,
        nmt_procure_po_row.*,
        SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.net_amount) ELSE 0 END) AS billed_amount,
		SUM(CASE WHEN fin_vendor_invoice_row.is_active =1 THEN (fin_vendor_invoice_row.quantity) ELSE 0 END) AS billed_qty
    FROM nmt_procure_po_row
    LEFT JOIN fin_vendor_invoice_row
	ON nmt_procure_po_row.id = fin_vendor_invoice_row.po_row_id
    WHERE nmt_procure_po_row.is_active =1
   GROUP BY nmt_procure_po_row.id
        
)AS
nmt_procure_po_row
ON nmt_procure_po.id = nmt_procure_po_row.po_id
        
WHERE 1
";
}
