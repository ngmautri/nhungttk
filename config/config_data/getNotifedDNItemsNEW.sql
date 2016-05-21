select
	mla_delivery_items.*,
	sum(mla_delivery_items.delivered_quantity) as notified_delivered_quantity
from
(
select 
	mla_delivery_items.*,
	mla_delivery_items_workflows.status as dn_last_status,
	mla_delivery_items_workflows.updated_on as dn_last_status_on,
	mla_delivery_items_workflows.updated_by as dn_last_status_by
from mla_delivery_items
left join mla_delivery_items_workflows
on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
)
as mla_delivery_items
where mla_delivery_items.dn_last_status = 'Notified'

group by mla_delivery_items.pr_item_id
