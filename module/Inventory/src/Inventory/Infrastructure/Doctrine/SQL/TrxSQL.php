<?php
namespace Inventory\Infrastructure\Doctrine\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxSQL
{

    const TRX_ROWS_DETAIL = "
SELECT
	nmt_inventory_trx.*,
	ifnull(nmt_inventory_fifo_layer.onhand_quantity,0) as onhand_qty
FROM nmt_inventory_trx
LEFT JOIN nmt_inventory_fifo_layer
ON nmt_inventory_fifo_layer.source_token = nmt_inventory_trx.uuid
WHERE 1 %s";

    const MV_DETAIL = "
SELECT
	nmt_inventory_mv.*,
   	SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN  1 ELSE 0 END) AS total_active_row,
    SUM(CASE WHEN (nmt_inventory_trx.doc_status='posted') THEN  1 ELSE 0 END) AS total_posted_row,
    SUM(CASE WHEN (nmt_inventory_trx.doc_status='draft') THEN  1 ELSE 0 END) AS total_draft_row,
    SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN nmt_inventory_trx.net_amount ELSE 0 END) AS total_net_amount,
    SUM(CASE WHEN (nmt_inventory_trx.is_active=1) THEN nmt_inventory_trx.gross_amount ELSE 0 END) AS total_gross_amount,

    COUNT(nmt_inventory_trx.movement_id) AS total_rows,
    SUM(CASE WHEN (nmt_inventory_mv.movement_flow='in' AND IFNULL(nmt_inventory_trx.quantity,0)=0) THEN  1 ELSE 0 END) AS zero_qty_rows	,
    SUM(CASE WHEN (nmt_inventory_mv.movement_flow='in' AND (quantity = nmt_inventory_trx.onhand_qty)) THEN  1 ELSE 0 END) AS un_used_rows,
    SUM(CASE WHEN (nmt_inventory_mv.movement_flow='in' AND (nmt_inventory_trx.quantity - nmt_inventory_trx.onhand_qty)>0) THEN  1 ELSE 0 END) AS exhausted_rows	
FROM nmt_inventory_mv

LEFT JOIN
(
	SELECT
		nmt_inventory_trx.*,		
		IFNULL(nmt_inventory_fifo_layer.onhand_quantity,0) AS onhand_qty
	FROM nmt_inventory_trx
	LEFT JOIN nmt_inventory_fifo_layer
	ON nmt_inventory_fifo_layer.source_token = nmt_inventory_trx.uuid
    WHERE nmt_inventory_trx.is_active=1 %s
) 
AS nmt_inventory_trx 
ON nmt_inventory_mv.id = nmt_inventory_trx.movement_id

WHERE 1 %s 
GROUP BY nmt_inventory_mv.id
";
}
