SELECT 
        A1.title, A1.firstname, A1.lastname, A2.*
    FROM
        mla_users AS A1
    JOIN (SELECT 
			B1.department_id,
            B1.user_id,
            B2.name AS department_name,
            B2.status AS department_status
    FROM
        mla_departments_members AS B1
    JOIN mla_departments AS B2 ON B1.department_id = B2.id) 
    
    AS A2 ON A1.id = A2.user_id
    
    /* Filer*/
     WHERE A2.user_id =39  
    
    