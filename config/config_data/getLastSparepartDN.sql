/* last article dn*/
select
	mla_spareparts_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_spareparts_last_dn.sparepart_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as sp_dn_last_status,   
			mla_delivery_items_workflows.updated_by as sp_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as sp_dn_last_status_on

		from mla_spareparts_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_spareparts_last_dn.last_workflow_id
	) 
	as mla_spareparts_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_spareparts_last_dn.dn_item_id
) 
as mla_spareparts_last_dn
join mla_vendors

on mla_vendors.id = mla_spareparts_last_dn.vendor_id 

/* last article dn*/