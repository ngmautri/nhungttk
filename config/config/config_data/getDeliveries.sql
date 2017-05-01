SELECT
	mla_delivery.*,
    year(mla_delivery.created_on) as dn_year,
    mla_delivery_items_1.totalItems,
    mla_delivery_1.status as dn_last_status,
	mla_delivery_1.dn_last_change,
    mla_delivery_1.dn_last_changed_by,
    mla_users_1.*
    
FROM mla_delivery 

LEFT JOIN
(

	SELECT 
	*
	FROM
	(
		SELECT 
		mla_delivery.*,
		mla_delivery_workflows_1.status,
		mla_delivery_workflows_1.dn_id_changed_on,
        mla_delivery_workflows_1.dn_last_changed_by

		FROM mla_delivery

		Left JOin
		(	
			select 
				mla_delivery_workflows.*,
				concat(mla_delivery_workflows.delivery_id,'+++',mla_delivery_workflows.updated_on) as dn_id_changed_on,
                mla_delivery_workflows.updated_by as dn_last_changed_by
			from mla_delivery_workflows
			
		) AS mla_delivery_workflows_1

		on mla_delivery_workflows_1.delivery_id = mla_delivery.id
	) AS mla_delivery_1

	JOIN

	(
		SELECT
			mla_delivery_workflows.delivery_id,
			MAX(mla_delivery_workflows.updated_on) AS dn_last_change,
			CONCAT(mla_delivery_workflows.delivery_id,'+++',MAX(mla_delivery_workflows.updated_on)) AS dn_id_lastchange_on
				
			FROM mla_delivery_workflows
		GROUP BY mla_delivery_workflows.delivery_id
	) AS mla_delivery_workflows_1

	on mla_delivery_workflows_1.dn_id_lastchange_on = mla_delivery_1.dn_id_changed_on
) AS mla_delivery_1
ON mla_delivery_1.id = mla_delivery.id

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
) AS mla_users_1

ON mla_users_1.user_id = mla_delivery.created_by

JOIN
(
	select 
		mla_delivery.id as dn_id, 
		count(*) as totalItems  
		
		from mla_delivery_items
	join mla_delivery 
	on mla_delivery_items.delivery_id = mla_delivery.id
	group by mla_delivery.id
) AS mla_delivery_items_1

ON mla_delivery_items_1.dn_id = mla_delivery.id

WHERE  mla_delivery.created_by = 39