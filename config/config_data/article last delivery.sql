select
	mla_delivery.*,
    mla_delivery_items.pr_item_id,
    mla_delivery_items.name as delivered_item_name,
    mla_delivery_items.delivered_quantity,
    mla_delivery_items.price,
	mla_delivery_items.currency,
	mla_delivery_items.vendor_id
from mla_delivery
join mla_delivery_items
on mla_delivery.id =  mla_delivery_items.delivery_id

join
(select mla_delivery.id as dn_id,count(*) as totalItems from mla_delivery_items
join mla_delivery
on mla_delivery_items.delivery_id = mla_delivery.id
group by mla_delivery.id) as mla_delivery_1

on mla_delivery_1.dn_id = mla_delivery.id