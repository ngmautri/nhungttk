insert into mla_purchase_request_items
(
	mla_purchase_request_items.purchase_request_id,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name,
    mla_purchase_request_items.EDT,
	mla_purchase_request_items.quantity,
    mla_purchase_request_items.article_id,
    mla_purchase_request_items.sparepart_id,
    mla_purchase_request_items.asset_id,
    mla_purchase_request_items.other_res_id,
    mla_purchase_request_items.remarks,    
    mla_purchase_request_items.created_on,
    mla_purchase_request_items.created_by
)
(
select
	11,
	mla_purchase_cart.priority,
    mla_purchase_cart.name,
    mla_purchase_cart.EDT,
	mla_purchase_cart.quantity,
    mla_purchase_cart.article_id,
    mla_purchase_cart.sparepart_id,
    mla_purchase_cart.asset_id,
    mla_purchase_cart.other_res_id,
    mla_purchase_cart.remarks,    
    mla_purchase_cart.created_on,
    mla_purchase_cart.created_by
from mla_purchase_cart
where 1
and mla_purchase_cart.status is null
and mla_purchase_cart.created_by = 39
)
