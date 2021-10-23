<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrHeaderSQL
{

    const PR_SQL = "
SELECT
    nmt_procure_pr.*,
    COUNT(nmt_procure_pr_row.id) AS total_row,

    SUM(CASE WHEN (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS std_gr_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_gr_qty, 0))>0 AND (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_gr_qty, 0)) < nmt_procure_pr_row.converted_standard_quantity  THEN  1 ELSE 0 END) AS std_gr_partial,
    
    SUM(CASE WHEN (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS std_ap_completed,
    SUM(CASE WHEN (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_ap_qty, 0))>0 AND (nmt_procure_pr_row.converted_standard_quantity - IFNULL(nmt_procure_pr_row.posted_standard_ap_qty, 0)) < nmt_procure_pr_row.converted_standard_quantity  THEN  1 ELSE 0 END) AS std_ap_partial
   
FROM nmt_procure_pr
LEFT JOIN
(
    %s
)
AS nmt_procure_pr_row
ON nmt_procure_pr_row.pr_id = nmt_procure_pr.id
WHERE 1
";

    const PR_PO_SQL = "
    SELECT
		nmt_procure_pr_row.pr_id AS pr_id,
		nmt_procure_pr.pr_auto_number AS pr_sys_number,
        'PO' as doc_type,
        nmt_procure_po_row.po_id as doc_id,
		nmt_procure_po_row.doc_token,
     	nmt_procure_po_row.doc_sys_number,           
        nmt_procure_po_row.doc_currency,
		SUM(nmt_procure_po_row.net_amount) as doc_net_amount,
        SUM(nmt_procure_po_row.local_net_amount) as local_net_amount,          
        nmt_procure_po_row.doc_posting_date,
        nmt_procure_po_row.doc_date,
		nmt_procure_po_row.doc_created_date
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
   
	JOIN 
    (
      SELECT 
			nmt_procure_po_row.*,
           	nmt_procure_po.currency_iso3 AS doc_currency,            
			nmt_procure_po.posting_date AS doc_posting_date,
			nmt_procure_po.doc_date AS doc_date,
            nmt_procure_po.created_on AS doc_created_date,
			nmt_procure_po.token AS doc_token,
            nmt_procure_po.sys_number AS doc_sys_number
		FROM nmt_procure_po_row
		LEFT JOIN nmt_procure_po
		ON nmt_procure_po.id = nmt_procure_po_row.po_id
        WHERE 1 %s
    ) AS nmt_procure_po_row
    
	ON nmt_procure_po_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %
    GROUP BY nmt_procure_po_row.po_id
";

    const PR_POGR_SQL = "
  SELECT
		nmt_procure_pr_row.pr_id AS pr_id,
		nmt_procure_pr.pr_auto_number AS pr_sys_number,
        'POGR' as doc_type,
        nmt_procure_gr_row.gr_id as doc_id,
		nmt_procure_gr_row.doc_token,
     	nmt_procure_gr_row.doc_sys_number,           
        nmt_procure_gr_row.doc_currency,
		SUM(nmt_procure_gr_row.net_amount) as doc_net_amount,
        SUM(nmt_procure_gr_row.net_amount) as local_net_amount,          
        nmt_procure_gr_row.doc_posting_date,
        nmt_procure_gr_row.doc_date,
		nmt_procure_gr_row.doc_created_date
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
   
	JOIN 
    (
      SELECT 
			nmt_procure_gr_row.*,
           	nmt_procure_gr.currency_iso3 AS doc_currency,            
			nmt_procure_gr.posting_date AS doc_posting_date,
			nmt_procure_gr.doc_date AS doc_date,
            nmt_procure_gr.created_on AS doc_created_date,
			nmt_procure_gr.token AS doc_token,
            nmt_procure_gr.sys_number AS doc_sys_number
		FROM nmt_procure_gr_row
		LEFT JOIN nmt_procure_gr
		ON nmt_procure_gr.id = nmt_procure_gr_row.gr_id
        WHERE 1 %s
    ) AS nmt_procure_gr_row
    
	ON nmt_procure_gr_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
    GROUP BY nmt_procure_gr_row.gr_id
";

    const PR_AP_SQL = "
  SELECT
		nmt_procure_pr_row.pr_id AS pr_id,
		nmt_procure_pr.pr_auto_number AS pr_sys_number,
        'AP' as doc_type,
        fin_vendor_invoice_row.invoice_id as doc_id,
		fin_vendor_invoice_row.doc_token,
     	fin_vendor_invoice_row.doc_sys_number,           
        fin_vendor_invoice_row.doc_currency,
		SUM(fin_vendor_invoice_row.net_amount) as doc_net_amount,
        SUM(fin_vendor_invoice_row.local_net_amount) as local_net_amount,          
        fin_vendor_invoice_row.doc_posting_date,
        fin_vendor_invoice_row.doc_date,
		fin_vendor_invoice_row.doc_created_date
	FROM nmt_procure_pr_row

    LEFT JOIN nmt_procure_pr
    ON nmt_procure_pr.id = nmt_procure_pr_row.pr_id
   
	JOIN 
    (
      SELECT 
			fin_vendor_invoice_row.*,
           	fin_vendor_invoice.currency_iso3 AS doc_currency,            
			fin_vendor_invoice.posting_date AS doc_posting_date,
			fin_vendor_invoice.doc_date AS doc_date,
            fin_vendor_invoice.created_on AS doc_created_date,
			fin_vendor_invoice.token AS doc_token,
            fin_vendor_invoice.sys_number AS doc_sys_number
		FROM fin_vendor_invoice_row
		LEFT JOIN fin_vendor_invoice
		ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
        WHERE 1 %s
    ) AS fin_vendor_invoice_row
    
	ON fin_vendor_invoice_row.pr_row_id = nmt_procure_pr_row.id
	WHERE 1 %s
    GROUP BY fin_vendor_invoice_row.invoice_id
";
}
