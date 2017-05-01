

/* SUBMITTED PR: with total items, last status, user and department*/
Select
*
from
(
		select
			mla_purchase_requests.*,
            year(mla_purchase_requests.requested_on) as pr_year,
			mla_purchase_request_items_1.totalItems,
			mla_purchase_requests_workflows_1_1.status as last_pr_status,
			mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
			mla_users_1.*

		from mla_purchase_requests

		left join
		(
			/* TOTAL PR Items*/
			select
				mla_purchase_requests.id as purchase_request_id, count(*) as totalItems
			from
				mla_purchase_request_items
			join mla_purchase_requests on mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
			group by mla_purchase_requests.id
			/* TOTAL PR Items*/
		) 
        
        as mla_purchase_request_items_1
		on mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

		join
		(
				/* Last Workflow changed PR) */
				select
					mla_purchase_requests_workflows_1.*
				from 

					(
						select 
						mla_purchase_requests_workflows.*,
						concat(mla_purchase_requests_workflows.purchase_request_id,'+++',mla_purchase_requests_workflows.updated_on) as pr_id_changed_on
						from mla_purchase_requests_workflows
					) 
                    as mla_purchase_requests_workflows_1

				join

					(
                    select 
						max(mla_purchase_requests_workflows.updated_on) as pr_last_change,
						concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
						mla_purchase_requests_workflows.purchase_request_id
						from mla_purchase_requests_workflows
					group by mla_purchase_requests_workflows.purchase_request_id
                    ) 
                    as mla_purchase_requests_workflows_2

				on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
			) 
            as mla_purchase_requests_workflows_1_1

		on mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

		join
			(
				/**USER-DEPARTMENT beginns*/
				select 
					mla_users.title, 
					mla_users.firstname, 
					mla_users.lastname, 
					mla_departments_members_1.*
				from mla_users
				join 
				(	
                select 
						mla_departments_members.department_id,
						mla_departments_members.user_id,
						mla_departments.name as department_name,
						mla_departments.status as department_status
					from mla_departments_members
					join mla_departments on mla_departments_members.department_id = mla_departments.id
					
					/* Filter*/
				) 
                as mla_departments_members_1 
				on mla_users.id = mla_departments_members_1.user_id
				/**USER-DEPARTMENT ends*/
				
			) 
        as mla_users_1
		on mla_users_1.user_id = mla_purchase_requests.requested_by
        
      
)

as mla_purchase_requests_sumbmited

Where 1
/* PR: with total items, last status, user and department*/