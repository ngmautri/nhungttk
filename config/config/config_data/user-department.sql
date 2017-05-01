	
    /**USER-DEPARTMENT beginns*/
    select
		mla_users.id,
        mla_users.title,
        mla_users.firstname, 
        mla_users.lastname,
        mla_users.email, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select
			mla_departments_members.user_id,
			mla_departments_members.department_id,
             mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
			join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
   where 1
    AND mla_users.id = 39
    
    /**USER-DEPARTMENT ends*/