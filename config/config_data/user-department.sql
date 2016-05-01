	
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