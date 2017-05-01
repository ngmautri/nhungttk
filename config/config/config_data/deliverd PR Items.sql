/*Delivered Quantity*/
select 
	mla_purchase_request_items.id as pr_item_id, 
	sum(mla_delivery_items.delivered_quantity) as delivered_quantity
    from mla_delivery_items

left join mla_purchase_request_items
On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

left join mla_delivery
on mla_delivery_items.delivery_id = mla_delivery.id

group by mla_delivery_items.pr_item_id
/*Delivered Quantity*/