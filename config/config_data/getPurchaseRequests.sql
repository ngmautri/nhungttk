Select
*
from
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status,
   concat (mla_users.firstname,' ',mla_users.lastname ) as pr_last_status_by_name

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on,
    mla_purchase_requests_totalitems.*
    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

left join 
(
	select
		mla_purchase_requests.id as pr_id,
		count(*) as totalItems
		from mla_purchase_requests
	left join mla_purchase_request_items
	on mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
	group by mla_purchase_request_items.purchase_request_id
) 
as mla_purchase_requests_totalitems

	on mla_purchase_requests_totalitems.pr_id = mla_purchase_requests.id
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
as mla_users_1
	
	on mla_users_1.user_id = mla_purchase_requests.pr_last_status_by
) 
as mla_purchase_requests

where 1

and pr_year = year(now())
and pr