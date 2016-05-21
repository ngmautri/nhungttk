select
*
from 
(
select 
	mla_delivery_items.id as dn_item_id,
    mla_delivery_items.delivery_id,
    mla_delivery_items.pr_item_id,
    mla_delivery_items.name as dn_item_name,
    mla_delivery_items.code as dn_item_code,
	mla_delivery_items.unit as dn_item_unit,
    mla_delivery_items.delivered_quantity,
	mla_delivery_items.price,
 	mla_delivery_items.currency,
  	mla_delivery_items.vendor_id,
	mla_delivery_items.created_by as delivered_by,

	mla_delivery_items_workflows.status as dn_item_last_status,
	mla_delivery_items_workflows.updated_on as dn_item_last_status_on,
	mla_delivery_items_workflows.updated_by as dn_item_last_status_by,
    mla_purchase_request_items.*
from mla_delivery_items

left join mla_delivery_items_workflows
on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id

left join 
(
	select 
		mla_purchase_requests.pr_number as pr_number,
  		mla_purchase_requests.name as pr_name,
        mla_purchase_requests.requested_by as pr_requester_id,
		mla_purchase_request_items.*
    from mla_purchase_request_items
    left join mla_purchase_requests
    on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
)
as mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items.pr_item_id
)
as mla_purchase_request_items
WHERE 1
AND mla_purchase_request_items.pr_item_id = 14
AND mla_purchase_request_items.dn_item_last_status='confirmed'
