select
*
(
select
mla_purchase_requests.*,
mla_purchase_request_items.*,
mla_purchase_requests_workflows.status as pr_status,
mla_purchase_requests_workflows.updated_on as pr_updated_on,
year(mla_purchase_requests.requested_on) as pr_year
from mla_purchase_requests
left join
(
	select 
		mla_purchase_requests.id as pr_id,
		count(*) as totalItems
	from mla_purchase_request_items 
	join mla_purchase_requests 
	on mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
	group by mla_purchase_requests.id
) 
as mla_purchase_request_items
	on mla_purchase_request_items.pr_id = mla_purchase_requests.id
left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id
)
as mla_purchase_requests
WHERE 1