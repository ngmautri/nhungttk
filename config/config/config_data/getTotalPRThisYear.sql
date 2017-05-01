Select
count(*) as total_pr_this_year,
mla_purchase_requests.pr_of_department_short_name
from
(
	select
		mla_purchase_requests.*,
		year(mla_purchase_requests.requested_on) as pr_year,
		
		concat (mla_users.firstname,' ',mla_users.lastname ) as requester_name,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_short_name as pr_of_department_short_name,
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
				mla_departments.short_name as department_short_name,
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

where 1
and pr_year = year(now())
AND mla_purchase_requests.pr_of_department_id
				= (SELECT department_id from mla_departments_members
				where user_id = 46 Limit 1)
	