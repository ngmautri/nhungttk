<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReportSQL
{

    const GR_LIST = "
SELECT
	nmt_procure_gr.*,
    COUNT(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.id) ELSE NULL END) AS total_row,
    SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.net_amount) ELSE 0 END) AS net_amount,
	SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.tax_amount) ELSE 0 END) AS tax_amount,
	SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.gross_amount) ELSE 0 END) AS gross_amount,
	SUM(CASE WHEN nmt_procure_gr_row.is_active =1 THEN (nmt_procure_gr_row.discount_rate*nmt_procure_gr_row.gross_amount) ELSE 0 END) AS gross_discount_amount   
FROM nmt_procure_gr        
LEFT JOIN nmt_procure_gr_row
ON nmt_procure_gr.id = nmt_procure_gr_row.gr_id        
WHERE 1
";
}
