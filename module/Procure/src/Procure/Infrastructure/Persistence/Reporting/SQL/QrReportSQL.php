<?php
namespace Procure\Infrastructure\Persistence\Reporting\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QrReportSQL
{

    const QR_LIST = "
SELECT
   nmt_procure_qo.*,
   count(nmt_procure_qo_row.id) as total_row,
   SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.net_amount) ELSE 0 END) as net_amount,
   SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.tax_amount) ELSE 0 END) as tax_amount,
   SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.gross_amount) ELSE 0 END) as gross_amount,
   SUM(CASE WHEN nmt_procure_qo_row.is_active =1 THEN (nmt_procure_qo_row.gross_amount*nmt_procure_qo_row.discount_rate) ELSE 0 END) as gross_discount_amount
FROM nmt_procure_qo
LEFT JOIN nmt_procure_qo_row
ON nmt_procure_qo_row.qo_id = nmt_procure_qo.id
WHERE 1
";

    const ALL_QR_ROW = "
SELECT
nmt_procure_qo_row.*
FROM nmt_procure_qo_row
LEFT JOIN nmt_procure_qo
ON nmt_procure_qo.id = nmt_procure_qo_row.qo_id
WHERE 1
";
}