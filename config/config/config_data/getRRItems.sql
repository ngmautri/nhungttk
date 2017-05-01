/* ALL PR ITEMS*/

select
*
from
(
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
 
  
    
	ifnull(mla_delivery_items_notified.notified_delivered_quantity,0) as notified_delivered_quantity,
	ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0) as confirmed_delivered_quantity,
	mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0)  as confirmed_balance

from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

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
  
/* total notified DN */
left join
(
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
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* total confirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as confirmed_delivered_quantity
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
	where mla_delivery_items.dn_last_status = 'confirmed'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id
)
as mla_purchase_request_items
Where 1

/* ALL PR ITEMS*/