/* Last Workflow changed PR ITEM) */
	select
		mla_purchase_requests_items_workflows_1.*
	from 

		(select 
		mla_purchase_requests_items_workflows.*,
		concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
		from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

	join

		(select 
			max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
			mla_purchase_requests_items_workflows.pr_item_id
			 from mla_purchase_requests_items_workflows
		group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

	on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
    
    group by mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
    
	/* Last Workflow changed PR ITEM) */