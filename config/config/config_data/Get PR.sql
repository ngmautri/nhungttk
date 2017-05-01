
SELECT 
    TB1.*, TB2.*, TB3.*, TB4.*
FROM
    
    mla_purchase_requests AS TB1
        
    JOIN
   
   (
    /* total pr items*/
    SELECT 
	mla_purchase_requests.id AS pr_id, COUNT(*) AS tItems 
    FROM
        mla_purchase_request_items
    JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
    GROUP BY mla_purchase_requests.id
    /* total pr items*/
  
    ) AS TB2 ON TB2.pr_id = TB1.id
     
     /* Left joint to show all item; Joint*/
    JOIN
    (
		/* Last PR status*/   
		SELECT 
			*
		FROM
			(SELECT 
				lt1.purchase_request_id,
				lt1.status AS last_status,
				lt2.last_change
		FROM
			(SELECT 
				lt3.*,
				CONCAT(lt3.purchase_request_id, '+++', lt3.updated_on) AS id_updated_on
			FROM
			mla_purchase_requests_workflows AS lt3) AS lt1
		JOIN (SELECT 
				CONCAT(tt1.purchase_request_id, '+++', MAX(tt1.updated_on)) AS id_lastchanged,
				tt1.purchase_request_id,
				MAX(tt1.updated_on) AS last_change
		FROM
			mla_purchase_requests_workflows AS tt1
		GROUP BY tt1.purchase_request_id) AS lt2 ON lt1.id_updated_on = lt2.id_lastchanged) AS lt4
		WHERE 1
		/* Last PR status*/
	) 
        
        AS TB3 ON TB1.id = TB3.purchase_request_id
       
	JOIN   
    (
     
		/* USER WITH DEPARTMENT*/   
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
			
		/* USER WITH DEPARTMENT*/   
      )
      
      AS TB4 ON TB4.user_id = TB1.requested_by