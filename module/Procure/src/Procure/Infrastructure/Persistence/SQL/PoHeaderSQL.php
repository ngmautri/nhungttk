<?php
namespace Procure\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoHeaderSQL
{

    const PO_SQL = "
SELECT
    nmt_procure_po.*,
    COUNT(nmt_procure_po_row.id) AS total_row,

    SUM(CASE WHEN (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_gr_qty, 0))<=0 THEN  1 ELSE 0 END) AS std_gr_completed,
    SUM(CASE WHEN (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_gr_qty, 0))>0 AND (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_gr_qty, 0)) < nmt_procure_po_row.converted_standard_quantity  THEN  1 ELSE 0 END) AS std_gr_partial,
    
    SUM(CASE WHEN (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_ap_qty, 0))<=0 THEN  1 ELSE 0 END) AS std_ap_completed,
    SUM(CASE WHEN (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_ap_qty, 0))>0 AND (nmt_procure_po_row.converted_standard_quantity - IFNULL(nmt_procure_po_row.posted_standard_ap_qty, 0)) < nmt_procure_po_row.converted_standard_quantity  THEN  1 ELSE 0 END) AS std_ap_partial
   
FROM nmt_procure_po
LEFT JOIN
(
    %s
)
AS nmt_procure_po_row
ON nmt_procure_po_row.po_id = nmt_procure_po.id
WHERE 1
";
}
