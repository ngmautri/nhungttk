/* PRINT SUBMITTED PR */
select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.seq_number_of_year,
    mla_purchase_requests.auto_pr_number,
    
    mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.status,
	mla_purchase_requests.updated_on,
	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	mla_purchase_requests.email,	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status

        
from mla_purchase_request_items

/* purchase requests*/
left join
(
	select
		mla_purchase_requests.*,
		year(mla_purchase_requests.requested_on) as pr_year,
		
		concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
		mla_users.email,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_status as pr_of_department_status

	from 
	(
			/* PURCHASE WITH STATUS = SUBMITTED*/
			select
			*
			from 
			(    
				select
					mla_purchase_requests.*,
					mla_purchase_requests_workflows.status,
					mla_purchase_requests_workflows.updated_on
				from mla_purchase_requests
				left join mla_purchase_requests_workflows
					on mla_purchase_requests_workflows.purchase_request_id = mla_purchase_requests.id
			)
			as mla_purchase_requests
			where 1 
			and mla_purchase_requests.status='submitted'
			/* PURCHASE WITH STATUS = SUBMITTED*/

	)
	as mla_purchase_requests

	left join
	(

			
		/**USER-DEPARTMENT beginns*/
		select 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname,
			mla_users.email, 
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
)
as mla_purchase_request_items
Where 1

/* PRINT SUBMITTED PR */	
			