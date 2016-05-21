/* Last Workflow changed PR) */
select
	mla_purchase_requests_workflows_1.*
from 
	/**/
	(
		select 
			mla_delivery_workflows.*,
			concat(mla_delivery_workflows.delivery_id,'+++',mla_delivery_workflows.updated_on) as dn_id_changed_on
		from mla_delivery_workflows 
	) as mla_purchase_requests_workflows_1

join

	(select 
		max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
		concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
		mla_purchase_requests_workflows.purchase_request_id
		 from mla_purchase_requests_workflows
	group by mla_purchase_requests_workflows.purchase_request_id) AS mla_purchase_requests_workflows_2

on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
/* Last Workflow changed PR) */