/* ALL PR ITEMS*/

select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
   
	ifnull(mla_delivery_items_confirmed.confirmed_quantity,0) as confirmed_quantity,
	ifnull(mla_delivery_items_rejected.rejected_quantity,0) as rejected_quantity,
	
    if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))
    ,0) as confirmed_balance,
	
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_confirmed.confirmed_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
    
    ifnull(mla_delivery_items_notified.unconfimed_quantity,0) as unconfirmed_quantity,
	ifnull( mla_delivery_cart.total_saved_delivered_quantity,0) mla_delivery_cart.total_saved_delivered_quantity as total_saved_delivered_quantity,
     
     last_sparepart_dn.vendor_name as sp_vendor_name,
   	last_sparepart_dn.vendor_id as sp_vendor_id,
	last_sparepart_dn.price as sp_price,
	last_sparepart_dn.currency as sp_currency,
    
	last_article_dn.vendor_name as article_vendor_name,
   	last_article_dn.vendor_id as article_vendor_id,
	last_article_dn.price as article_price,
	last_article_dn.currency as article_currency
	
from mla_purchase_request_items
	
/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on,
	
    year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status
	
from mla_purchase_requests
	
left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id
    
left join
(
	
	
    /**USER-DEPARTMENT beginns*/
    select
        mla_users.title,
        mla_users.firstname,
        mla_users.lastname,
        mla_departments_members_1.*
    from mla_users
    join
	(	select
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
	
)
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
)
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
	
/* total confirmed DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id
	
/* total rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_rejected
on mla_delivery_items_rejected.pr_item_id = mla_purchase_request_items.id

/* total notified /unconfirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as unconfimed_quantity
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
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* Check Saved Delivery Cart */
left join 
(
	select
		*,
		sum(mla_delivery_cart.delivered_quantity) as total_saved_delivered_quantity
	from mla_delivery_cart
	where mla_delivery_cart.status ="SAVED"
	group by mla_delivery_cart.pr_item_id
) 
as mla_delivery_cart
on mla_delivery_cart.pr_item_id = mla_purchase_request_items.id


/* Last Article DN */
left join
(
/* last article dn*/
select
	mla_articles_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_articles_last_dn.article_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as article_dn_last_status,   
			mla_delivery_items_workflows.updated_by as article_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as article_dn_last_status_on

		from mla_articles_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_articles_last_dn.last_workflow_id
	) 
	as mla_articles_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_articles_last_dn.dn_item_id
) 
as mla_articles_last_dn
join mla_vendors

on mla_vendors.id = mla_articles_last_dn.vendor_id 

/* last article dn*/

) 
as last_article_dn
on last_article_dn.article_id = mla_purchase_request_items.article_id

/* Last SP DN */
left join
(
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


) 
as last_sparepart_dn
on last_sparepart_dn.sparepart_id = mla_purchase_request_items.sparepart_id


Where 1
AND mla_purchase_requests.pr_last_status IS NOT NULL

/* ALL PR ITEMS*/