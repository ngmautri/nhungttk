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

    const BEGIN_GR_GI_END = "
SELECT
	nmt_inventory_trx.item_id,
	nmt_inventory_trx.wh_id,
	(IFNULL(nmt_inventory_trx1.total_gr,0)- IFNULL(nmt_inventory_trx1.total_gi,0)) AS begin_qty,
	IFNULL(nmt_inventory_trx2.total_gr,0) AS gr_qty,
	IFNULL(nmt_inventory_trx2.total_gi,0) AS gi_qty,
	(IFNULL(nmt_inventory_trx1.total_gr,0) - IFNULL(nmt_inventory_trx1.total_gi,0)+IFNULL(nmt_inventory_trx2.total_gr,0) - IFNULL(nmt_inventory_trx2.total_gi,0)) AS end_qty,
	(IFNULL(nmt_inventory_trx1.total_gr_vl,0) - IFNULL(nmt_inventory_trx1.total_gi_vl,0)) AS begin_vl,
	(IFNULL(nmt_inventory_trx2.total_gr_vl,0)) AS gr_vl,
	(IFNULL(nmt_inventory_trx2.total_gi_vl,0)) AS gi_vl,
	(IFNULL(nmt_inventory_trx1.total_gr_vl,0) - IFNULL(nmt_inventory_trx1.total_gi_vl,0)+IFNULL(nmt_inventory_trx2.total_gr_vl,0) - IFNULL(nmt_inventory_trx2.total_gi_vl,0)) AS end_vl

FROM nmt_inventory_trx

LEFT JOIN
(
	SELECT
	nmt_inventory_trx.item_id,
	nmt_inventory_trx.wh_id,
	SUM(CASE WHEN nmt_inventory_trx.flow ='IN' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gr,
	SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gi,
	SUM(CASE WHEN nmt_inventory_trx.flow ='IN' THEN (nmt_inventory_trx.quantity*nmt_inventory_trx.doc_unit_price*nmt_inventory_trx.exchange_rate) ELSE 0 END) AS total_gr_vl,
	SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.cogs_local) ELSE 0 END) AS total_gi_vl


	FROM nmt_inventory_trx
	LEFT JOIN nmt_inventory_mv
	ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
	WHERE 1 %s	
	GROUP BY nmt_inventory_trx.wh_id,nmt_inventory_trx.item_id
)
AS nmt_inventory_trx1
ON nmt_inventory_trx1.item_id = nmt_inventory_trx.item_id AND nmt_inventory_trx1.wh_id = nmt_inventory_trx.wh_id
LEFT JOIN
(
	SELECT
		nmt_inventory_trx.item_id,
		nmt_inventory_trx.wh_id,
		SUM(CASE WHEN nmt_inventory_trx.flow ='IN' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gr,
		SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_gi,
		SUM(CASE WHEN nmt_inventory_trx.flow ='IN' THEN (nmt_inventory_trx.quantity*nmt_inventory_trx.doc_unit_price*nmt_inventory_trx.exchange_rate) ELSE 0 END) AS total_gr_vl,
		SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.cogs_local) ELSE 0 END) AS total_gi_vl
	FROM nmt_inventory_trx
	LEFT JOIN nmt_inventory_mv
	ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
	WHERE 1 %s
	GROUP BY nmt_inventory_trx.wh_id,nmt_inventory_trx.item_id
)
AS nmt_inventory_trx2
ON nmt_inventory_trx2.item_id = nmt_inventory_trx.item_id AND nmt_inventory_trx2.wh_id = nmt_inventory_trx.wh_id

WHERE 1 %s 
GROUP BY nmt_inventory_trx.wh_id, nmt_inventory_trx.item_id
";

    const COST_ISSUE_FOR = "
SELECT
	nmt_inventory_trx.issue_for,
    nmt_inventory_trx.item_id,
    nmt_inventory_item.item_name,   
	nmt_inventory_trx.wh_id,
	(IFNULL(nmt_inventory_trx1.total_consume_qty,0)) AS begin_consume_qty,
    (IFNULL(nmt_inventory_trx1.total_consume_vl,0)) AS begin_consume_vl,
 
    (IFNULL(nmt_inventory_trx2.total_consume_qty,0)) AS consume_qty,
	(IFNULL(nmt_inventory_trx2.total_consume_vl,0)) AS consume_vl,
	(IFNULL(nmt_inventory_trx1.total_consume_qty,0)+ IFNULL(nmt_inventory_trx2.total_consume_qty,0)) AS end_consume_qty,
	(IFNULL(nmt_inventory_trx1.total_consume_vl,0)+ IFNULL(nmt_inventory_trx2.total_consume_vl,0)) AS end_consume_vl

FROM nmt_inventory_trx

LEFT join nmt_inventory_item
on nmt_inventory_item.id = nmt_inventory_trx.item_id

LEFT JOIN
(
	SELECT
	nmt_inventory_trx.issue_for,
	nmt_inventory_trx.wh_id,
    nmt_inventory_trx.item_id,
	SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_consume_qty,
	SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.cogs_local) ELSE 0 END) AS total_consume_vl

	FROM nmt_inventory_trx
	LEFT JOIN nmt_inventory_mv
	ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
	WHERE 1 %s
	GROUP BY nmt_inventory_trx.wh_id, nmt_inventory_trx.item_id
)
AS nmt_inventory_trx1
ON nmt_inventory_trx1.issue_for = nmt_inventory_trx.issue_for AND nmt_inventory_trx1.wh_id = nmt_inventory_trx.wh_id
LEFT JOIN
(
	SELECT
		nmt_inventory_trx.issue_for,
        nmt_inventory_trx.item_id,
		nmt_inventory_trx.wh_id,
        
		SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.quantity) ELSE 0 END) AS total_consume_qty,
		SUM(CASE WHEN nmt_inventory_trx.flow ='OUT' THEN (nmt_inventory_trx.cogs_local) ELSE 0 END) AS total_consume_vl
	FROM nmt_inventory_trx
	LEFT JOIN nmt_inventory_mv
	ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id
	WHERE 1 %s
	GROUP BY nmt_inventory_trx.wh_id, nmt_inventory_trx.item_id
)
AS nmt_inventory_trx2
ON nmt_inventory_trx2.issue_for = nmt_inventory_trx.issue_for AND nmt_inventory_trx2.wh_id = nmt_inventory_trx.wh_id

WHERE 1 %s 
GROUP BY nmt_inventory_trx.wh_id, nmt_inventory_trx.item_id

";
}
