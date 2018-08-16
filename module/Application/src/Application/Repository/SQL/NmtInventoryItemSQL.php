<?php
namespace Application\Repository\SQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NmtInventoryItemSQL
{
    
    /**
     * SQL for get inventory stock
     * @var string
     */
    const ITEM_STOCK_SQL = "
    SELECT 
    	nmt_inventory_item.id,
      	SUM(nmt_inventory_fifo_layer.quantity)  AS total_gr,
        SUM(nmt_inventory_fifo_layer.quantity*nmt_inventory_fifo_layer.doc_unit_price*nmt_inventory_fifo_layer.exchange_rate) AS total_gr_value,
        SUM(nmt_inventory_fifo_layer.onhand_quantity) AS total_onhand,
    	SUM(nmt_inventory_fifo_layer.onhand_quantity*nmt_inventory_fifo_layer.doc_unit_price) AS total_onhand_value,
    	SUM(nmt_inventory_fifo_layer.onhand_quantity*nmt_inventory_fifo_layer.doc_unit_price*nmt_inventory_fifo_layer.exchange_rate) AS total_onhand_local_value,
      	nmt_inventory_fifo_layer_consume. total_gi,
        nmt_inventory_fifo_layer_consume. total_gi_value,
    	nmt_inventory_item_exchange.total_ex
        
    FROM nmt_inventory_item
    
    LEFT JOIN nmt_inventory_fifo_layer
    ON nmt_inventory_item.id = nmt_inventory_fifo_layer.item_id
    
    LEFT JOIN
    (
        SELECT 
        	nmt_inventory_item.id AS item_id,
          	SUM(nmt_inventory_fifo_layer_consume.quantity)  AS total_gi,
            SUM(nmt_inventory_fifo_layer_consume.total_value) AS total_gi_value
        FROM nmt_inventory_item
        
        JOIN nmt_inventory_fifo_layer_consume
        ON nmt_inventory_item.id = nmt_inventory_fifo_layer_consume.item_id
        WHERE 1 %s
        GROUP BY nmt_inventory_item.id
    )
    
    AS nmt_inventory_fifo_layer_consume
    ON nmt_inventory_fifo_layer_consume.item_id=nmt_inventory_item.id
    
    LEFT JOIN
    (
    
        SELECT 
        	nmt_inventory_item.id AS item_id,
          	SUM(nmt_inventory_item_exchange.quantity)  AS total_ex
         FROM nmt_inventory_item
        
        JOIN nmt_inventory_item_exchange
        ON nmt_inventory_item.id = nmt_inventory_item_exchange.item_id
        WHERE 1 %s
        GROUP BY nmt_inventory_item.id
    )
    
    AS nmt_inventory_item_exchange
    ON nmt_inventory_item_exchange.item_id=nmt_inventory_item.id
    
    WHERE 1 %s
    GROUP BY nmt_inventory_item.id
";


}

