SELECT 
mla_purchase_request_items_1.*,

mla_delivery_items_4.vendor_name as sp_vendor_name,
mla_delivery_items_3.vendor_name as article_vendor_name,
mla_delivery_items_4.price as sp_price,
mla_delivery_items_3.price as article_price,
mla_delivery_items_4.vendor_id as sp_vendor_id,
mla_delivery_items_3.vendor_id as article_vendor_id,
mla_delivery_items_4.currency as sp_currency,
mla_delivery_items_3.currency as article_currency
FROM 
(

	SElECT 
		mla_purchase_request_items.*,
		ifnull(mla_delivery_items_1.delivered_quantity,0) as delivered_quantity,
		(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_1.delivered_quantity,0)) as balance,
		mla_purchase_requests_1.pr_number,
        year(mla_purchase_requests_1.requested_on) as pr_year,
          
		mla_purchase_requests_1.last_pr_status,
		mla_purchase_requests_1.last_pr_status_on,
		mla_purchase_requests_1.department_id,
		mla_purchase_requests_1.department_name,
		mla_purchase_requests_1.requested_by,
	 
		
		mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
		mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
	 
	FROM mla_purchase_request_items

	LEFT JOIN
	(
		/*Delivered Quantity*/
		select 
			mla_purchase_request_items.id as pr_item_id, 
			sum(mla_delivery_items.delivered_quantity) as delivered_quantity
			from mla_delivery_items

		left join mla_purchase_request_items
		On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

		left join mla_delivery
		on mla_delivery_items.delivery_id = mla_delivery.id

		group by mla_delivery_items.pr_item_id
		/*Delivered Quantity*/
	) 
    
    AS mla_delivery_items_1

	ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

	/* JOIN: select only submitted PR */
	JOIN 
	(
		/* PR: with total items, last status, user and department*/

		SELECT
			mla_purchase_requests.*,
			mla_purchase_request_items_1.tItems as totalItems,
			mla_purchase_requests_workflows_1_1.status as last_pr_status,
			mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
			mla_users_1.*

		FROM mla_purchase_requests

		LEFT JOIN
		(
			/* TOTAL PR Items*/
			SELECT
				mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
			FROM
				mla_purchase_request_items
			JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
			GROUP BY mla_purchase_requests.id
			/* TOTAL PR Items*/

		) 
        
        AS mla_purchase_request_items_1
		ON mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

		JOIN
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
						max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
						concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
						mla_purchase_requests_workflows.purchase_request_id
						from mla_purchase_requests_workflows
					group by mla_purchase_requests_workflows.purchase_request_id
                    ) 
                    AS mla_purchase_requests_workflows_2

				on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
				
				/* FILTER  
				WHERE mla_purchase_requests_workflows_1.status='Approved'*/
				/* Last Workflow changed PR) */
			) 
            AS mla_purchase_requests_workflows_1_1

			ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

			JOIN
			(
				/**USER-DEPARTMENT beginns*/
				SELECT 
					mla_users.title, 
					mla_users.firstname, 
					mla_users.lastname, 
					mla_departments_members_1.*
				FROM mla_users
				JOIN 
				(	
                SELECT 
						mla_departments_members.department_id,
						mla_departments_members.user_id,
						mla_departments.name AS department_name,
						mla_departments.status AS department_status
					FROM mla_departments_members
					JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
					
					/* Filter*/
				) 
                AS mla_departments_members_1 
				ON mla_users.id = mla_departments_members_1.user_id
				/**USER-DEPARTMENT ends*/
				
			) 
            AS mla_users_1
			ON mla_users_1.user_id = mla_purchase_requests.requested_by

	) 
    AS mla_purchase_requests_1

	ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

	LEFT JOIN
	(
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

	) 
    AS mla_purchase_requests_items_workflows_1_1

	ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

	LEFT JOIN
	(
		  /**USER-DEPARTMENT beginns*/
		SELECT 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname, 
			mla_departments_members_1.*
		FROM mla_users
		JOIN 
		(	
			SELECT 
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name AS department_name,
				mla_departments.status AS department_status
			FROM mla_departments_members
			JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
		) 
        AS mla_departments_members_1 
		ON mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/
	) as mla_users_1

	ON mla_users_1.user_id = mla_purchase_request_items.created_by
)  

AS mla_purchase_request_items_1

/* LAST ARTICLE DO */
left JOIN
(
	/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

	SELECT 
		mla_delivery_items_2.*
	FROM
	(

		/* DELIVERY ITEMS TABLE WITH LAST DO*/
		select
			mla_delivery_items_1.*,
			mla_last_article_do.article_do_last_change,
			mla_last_sparepart_do.sparepart_do_last_change
		from
		(
			/*Delivery Items Table*/
			select 
				mla_delivery_items.*,
				concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_key,
				mla_purchase_request_items.article_id,
				concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_key,
				mla_purchase_request_items.sparepart_id,
				concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_key,
				mla_purchase_request_items.asset_id,
				mla_vendors.name as vendor_name
			 
			from mla_delivery_items

			join mla_purchase_request_items
			on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

			join mla_vendors
			on mla_vendors.id = mla_delivery_items.vendor_id
		)
		As mla_delivery_items_1

		left JOIN
		(
			/*Last Article DO KEY*/
			select 

				mla_delivery_items_1.article_id,
				max(mla_delivery_items_1.created_on) as article_do_last_change,
				concat(mla_delivery_items_1.article_id,'+++',max(mla_delivery_items_1.created_on)) as article_last_do_key

			from 
			(
				/*Delivery Items Table*/
				select 
					mla_delivery_items.*,
					concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
					mla_purchase_request_items.article_id,
					concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_date,
					mla_purchase_request_items.sparepart_id,
					concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_date,
					mla_purchase_request_items.asset_id,
					mla_vendors.name as vendor_name
				 
				from mla_delivery_items

				join mla_purchase_request_items
				on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

				join mla_vendors
				on mla_vendors.id = mla_delivery_items.vendor_id
				/*Delivery Items Table*/

			) as mla_delivery_items_1

			group by mla_delivery_items_1.article_id
			/*Last Article DO KEY*/
		) 
		AS mla_last_article_do

		ON mla_last_article_do.article_last_do_key = mla_delivery_items_1.article_do_key

		 left JOIN
		(
		/*Last Spartpart DO KEY*/

		select

			mla_delivery_items_1.sparepart_id,
			max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
			concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as sparepart_last_do_key

		from 
		(
			/*Delivery Items Table*/
			select 
				mla_delivery_items.*,
				concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
				mla_purchase_request_items.article_id,
				concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as sparepart_do_date,
				mla_purchase_request_items.sparepart_id,
				concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_last_do,
				mla_purchase_request_items.asset_id,
				mla_vendors.name as vendor_name
			 
			from mla_delivery_items

			join mla_purchase_request_items
			on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

			join mla_vendors
			on mla_vendors.id = mla_delivery_items.vendor_id
			/*Delivery Items Table*/

		) as mla_delivery_items_1

		group by mla_delivery_items_1.sparepart_id

		/*Last Spartpart DO KEY*/

			
		)
		AS mla_last_sparepart_do

		ON mla_last_sparepart_do.sparepart_last_do_key = mla_delivery_items_1.spartpart_do_key
		/* DELIVERY ITEMS TABLE WITH LAST DO*/
)

	AS mla_delivery_items_2

	WHERE mla_delivery_items_2.article_do_last_change  IS NOT NULL OR mla_delivery_items_2.sparepart_do_last_change IS NOT NULL

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

) 

AS mla_delivery_items_3

ON mla_purchase_request_items_1.article_id = mla_delivery_items_3.article_id


/* LAST SP DO */
left JOIN
(
/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

SELECT 
	mla_delivery_items_2.*
FROM
(

	/* DELIVERY ITEMS TABLE WITH LAST DO*/
	select
		mla_delivery_items_1.*,
		mla_last_article_do.article_do_last_change,
		mla_last_sparepart_do.sparepart_do_last_change
	from
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_key,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_key,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_key,
			mla_purchase_request_items.asset_id,
			mla_vendors.name as vendor_name
		 
		from mla_delivery_items

		join mla_purchase_request_items
		on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

		join mla_vendors
		on mla_vendors.id = mla_delivery_items.vendor_id
	)
	As mla_delivery_items_1

	left JOIN
	(
		/*Last Article DO KEY*/
		select 

			mla_delivery_items_1.article_id,
			max(mla_delivery_items_1.created_on) as article_do_last_change,
			concat(mla_delivery_items_1.article_id,'+++',max(mla_delivery_items_1.created_on)) as article_last_do_key

		from 
		(
			/*Delivery Items Table*/
			select 
				mla_delivery_items.*,
				concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
				mla_purchase_request_items.article_id,
				concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_date,
				mla_purchase_request_items.sparepart_id,
				concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_date,
				mla_purchase_request_items.asset_id,
				mla_vendors.name as vendor_name
			 
			from mla_delivery_items

			join mla_purchase_request_items
			on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

			join mla_vendors
			on mla_vendors.id = mla_delivery_items.vendor_id
			/*Delivery Items Table*/

		) as mla_delivery_items_1

		group by mla_delivery_items_1.article_id
		/*Last Article DO KEY*/
	) 
	AS mla_last_article_do

	ON mla_last_article_do.article_last_do_key = mla_delivery_items_1.article_do_key

	 left JOIN
	(
	/*Last Spartpart DO KEY*/

	select

		mla_delivery_items_1.sparepart_id,
		max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
		concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as sparepart_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as sparepart_do_date,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_last_do,
			mla_purchase_request_items.asset_id,
			mla_vendors.name as vendor_name
		 
		from mla_delivery_items

		join mla_purchase_request_items
		on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

		join mla_vendors
		on mla_vendors.id = mla_delivery_items.vendor_id
		/*Delivery Items Table*/

	) as mla_delivery_items_1

	group by mla_delivery_items_1.sparepart_id

	/*Last Spartpart DO KEY*/

		
	)
	AS mla_last_sparepart_do

	ON mla_last_sparepart_do.sparepart_last_do_key = mla_delivery_items_1.spartpart_do_key
	/* DELIVERY ITEMS TABLE WITH LAST DO*/
)

AS mla_delivery_items_2

WHERE mla_delivery_items_2.article_do_last_change  IS NOT NULL OR mla_delivery_items_2.sparepart_do_last_change IS NOT NULL

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

) 

AS mla_delivery_items_4

ON mla_purchase_request_items_1.sparepart_id = mla_delivery_items_4.sparepart_id

