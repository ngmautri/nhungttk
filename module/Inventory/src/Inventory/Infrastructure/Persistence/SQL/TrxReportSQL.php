<?php
namespace Inventory\Infrastructure\Persistence\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxReportSQL
{

    const TRX_LIST = "
SELECT
	nmt_inventory_mv.*,
    COUNT(CASE WHEN nmt_inventory_trx.is_active =1 THEN (nmt_inventory_trx.id) ELSE NULL END) AS total_row
 FROM nmt_inventory_mv        
LEFT JOIN nmt_inventory_trx
ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id        
WHERE 1
";

    const TRX_ROWS = "
SELECT

nmt_inventory_trx.*
        
FROM nmt_inventory_trx
        
LEFT JOIN nmt_inventory_mv
ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
        
WHERE 1
";
}
