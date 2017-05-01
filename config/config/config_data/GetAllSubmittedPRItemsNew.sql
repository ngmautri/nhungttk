select
*
from
(

select
	mla_purchase_request_items.*,
	mla_purchase_requests_sumbmited.last_pr_status,
    mla_purchase_requests_sumbmited.department_name,
	mla_purchase_requests_sumbmited.department_id,
    concat(mla_purchase_requests_sumbmited.firstname,' ', mla_purchase_requests_sumbmited.lastname) as requester,
    mla_purchase_requests_sumbmited.user_id as requester_id,
     
    mla_purchase_requests_sumbmited.pr_year,
    ifnull(mla_delivery_item_confirmed_2.delivered_quantity_confirmed,0) as total_delivered_quantity_confirmed,
	ifnull(mla_delivery_item_confirmed_3.delivered_quantity_confirmed,0) as total_delivered_quantity_notified,
	mla_purchase_request_items.quantity -    ifnull(mla_delivery_item_confirmed_2.delivered_quantity_confirmed,0) as confirmed_balance	
    
from mla_purchase_request_items

join /* join submitted PR */
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
on mla_purchase_request_items.purchase_request_id = mla_purchase_requests_sumbmited.id

/* Confirmed delivered Items */
left join
(
	
	/* Total delivered quantity confirmed for PR ITEM */

	select
	mla_delivery_item_confirmed_1.pr_item_id,
	sum(mla_delivery_item_confirmed_1.delivered_quantity) as delivered_quantity_confirmed

	from
	(

		/* Confirmed DN Items*/
		Select 
			mla_delivery_item_confirmed.*,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.article_id,'+++', mla_delivery_item_confirmed.last_status_on) as article_id_dn_last_confirmed_key,
			mla_purchase_request_items.asset_id,
			concat(mla_purchase_request_items.asset_id,'+++', mla_delivery_item_confirmed.last_status_on) as asset_id_dn_last_confirmed_key,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.sparepart_id,'+++', mla_delivery_item_confirmed.last_status_on) as sparepart_id_dn_last_confirmed_key

		from 
		(	
			/* DN Items with Last Status */
			select 
			mla_delivery_items_1.*,
			mla_vendors.name as vendor_name
			from 
			(
				select
					mla_delivery_items.*,
					mla_delivery_items_workflows_1_1.status as last_status,
					mla_delivery_items_workflows_1_1.updated_on as last_status_on,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_name as last_status_by_name,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_id as last_status_by_id,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_member_of_department_name as last_status_by_member_of_name,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_member_of_department_id last_status_by_member_of_id
				from 
				mla_delivery_items

				left join

				(

					/*** TABLE LAST STATUS OF DN ITEM ***/
					select 
						mla_delivery_items_workflows_1.*,
						concat(mla_users_1.firstname,' ', mla_users_1.lastname) as dn_item_updated_by_name,
						mla_users_1.user_id as dn_item_updated_by_id,

						mla_users_1.department_name as dn_item_updated_by_member_of_department_name,
						mla_users_1.department_id as dn_item_updated_by_member_of_department_id

					from
					(
						select
						mla_delivery_items_workflows.*,
						concat(mla_delivery_items_workflows.dn_item_id,'+++',mla_delivery_items_workflows.updated_on) as dn_item_updated_key
						from mla_delivery_items_workflows

					)

					as mla_delivery_items_workflows_1

					join
					(
						select
							mla_delivery_items_workflows.dn_item_id,
							concat(mla_delivery_items_workflows.dn_item_id,'+++',max(mla_delivery_items_workflows.updated_on)) as dn_item_last_updated_key
						from mla_delivery_items_workflows
						group by mla_delivery_items_workflows.dn_item_id
					) 
					as mla_delivery_items_workflows_2

					on mla_delivery_items_workflows_2.dn_item_last_updated_key = mla_delivery_items_workflows_1.dn_item_updated_key

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
					on mla_users_1.user_id =  mla_delivery_items_workflows_1.updated_by

					/*** TABLE LAST STATUS OF DN ITEM ***/

				)

				as mla_delivery_items_workflows_1_1

				on mla_delivery_items_workflows_1_1.dn_item_id = mla_delivery_items.id
			)

			as mla_delivery_items_1

			join mla_vendors
			on mla_vendors.id =  mla_delivery_items_1.vendor_id

			where last_status ='Confirmed'
			/* DN Items with Last Status */
		)
		as mla_delivery_item_confirmed

		join mla_purchase_request_items
		on mla_purchase_request_items.id = mla_delivery_item_confirmed.pr_item_id

		/* Confirmed DN Items*/
		) 
	as mla_delivery_item_confirmed_1
	group by mla_delivery_item_confirmed_1.pr_item_id
	/* Total delivered quantity confirmed for PR ITEM */

)
as mla_delivery_item_confirmed_2
on mla_delivery_item_confirmed_2.pr_item_id = mla_purchase_request_items.id

/* Notified delivered Items */
left join
(
	
	/* Total delivered quantity confirmed for PR ITEM */

	select
	mla_delivery_item_confirmed_1.pr_item_id,
	sum(mla_delivery_item_confirmed_1.delivered_quantity) as delivered_quantity_confirmed

	from
	(

		/* Confirmed DN Items*/
		Select 
			mla_delivery_item_confirmed.*,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.article_id,'+++', mla_delivery_item_confirmed.last_status_on) as article_id_dn_last_confirmed_key,
			mla_purchase_request_items.asset_id,
			concat(mla_purchase_request_items.asset_id,'+++', mla_delivery_item_confirmed.last_status_on) as asset_id_dn_last_confirmed_key,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.sparepart_id,'+++', mla_delivery_item_confirmed.last_status_on) as sparepart_id_dn_last_confirmed_key

		from 
		(	
			/* DN Items with Last Status */
			select 
			mla_delivery_items_1.*,
			mla_vendors.name as vendor_name
			from 
			(
				select
					mla_delivery_items.*,
					mla_delivery_items_workflows_1_1.status as last_status,
					mla_delivery_items_workflows_1_1.updated_on as last_status_on,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_name as last_status_by_name,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_id as last_status_by_id,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_member_of_department_name as last_status_by_member_of_name,
					mla_delivery_items_workflows_1_1.dn_item_updated_by_member_of_department_id last_status_by_member_of_id
				from 
				mla_delivery_items

				left join

				(
					/*** TABLE LAST STATUS OF DN ITEM ***/
					select 
						mla_delivery_items_workflows_1.*,
						concat(mla_users_1.firstname,' ', mla_users_1.lastname) as dn_item_updated_by_name,
						mla_users_1.user_id as dn_item_updated_by_id,

						mla_users_1.department_name as dn_item_updated_by_member_of_department_name,
						mla_users_1.department_id as dn_item_updated_by_member_of_department_id

					from
					(
						select
						mla_delivery_items_workflows.*,
						concat(mla_delivery_items_workflows.dn_item_id,'+++',mla_delivery_items_workflows.updated_on) as dn_item_updated_key
						from mla_delivery_items_workflows

					)

					as mla_delivery_items_workflows_1

					join
					(
						select
							mla_delivery_items_workflows.dn_item_id,
							concat(mla_delivery_items_workflows.dn_item_id,'+++',max(mla_delivery_items_workflows.updated_on)) as dn_item_last_updated_key
						from mla_delivery_items_workflows
						group by mla_delivery_items_workflows.dn_item_id
					) 
					as mla_delivery_items_workflows_2

					on mla_delivery_items_workflows_2.dn_item_last_updated_key = mla_delivery_items_workflows_1.dn_item_updated_key

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
					on mla_users_1.user_id =  mla_delivery_items_workflows_1.updated_by

					/*** TABLE LAST STATUS OF DN ITEM ***/

				)

				as mla_delivery_items_workflows_1_1

				on mla_delivery_items_workflows_1_1.dn_item_id = mla_delivery_items.id
			)

			as mla_delivery_items_1

			join mla_vendors
			on mla_vendors.id =  mla_delivery_items_1.vendor_id

			where last_status ='Notified'
			/* DN Items with Last Status */
		)
		as mla_delivery_item_confirmed

		join mla_purchase_request_items
		on mla_purchase_request_items.id = mla_delivery_item_confirmed.pr_item_id

		/* Confirmed DN Items*/
		) 
	as mla_delivery_item_confirmed_1
	group by mla_delivery_item_confirmed_1.pr_item_id
	/* Total delivered quantity confirmed for PR ITEM */

)
as mla_delivery_item_confirmed_3
on mla_delivery_item_confirmed_3.pr_item_id = mla_purchase_request_items.id
) as mla_purchase_request_items_1

WHERE 1