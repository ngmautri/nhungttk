select 
	mla_delivery_items_workflows.*,
	mla_delivery_items.*
from mla_delivery_items_workflows

left join mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items_workflows.pr_item_id

left join mla_delivery_items
on mla_delivery_items.id = mla_delivery_items_workflows.dn_item_id

where 1
AND mla_purchase_request_items.id=14
order by mla_delivery_items_workflows.updated_on
