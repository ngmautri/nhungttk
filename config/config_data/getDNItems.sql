Select 
*
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

	Left join

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
			SELECT 
				mla_users.title, 
				mla_users.firstname, 
				mla_users.lastname, 
				mla_departments_members_1.*
			FROM mla_users
			JOIN 
			(	SELECT 
					mla_departments_members.department_id,
					mla_departments_members.user_id,
					mla_departments.name AS department_name,
					mla_departments.status AS department_status
				FROM mla_departments_members
				JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
			) AS mla_departments_members_1 
			ON mla_users.id = mla_departments_members_1.user_id
			/**USER-DEPARTMENT ends*/
		)

		as mla_users_1
		on mla_users_1.user_id =  mla_delivery_items_workflows_1.updated_by

		/*** TABLE LAST STATUS OF DN ITEM ***/

	)

	AS mla_delivery_items_workflows_1_1

	on mla_delivery_items_workflows_1_1.dn_item_id = mla_delivery_items.id
)

AS mla_delivery_items_1
Where last_status = 'Notified'
