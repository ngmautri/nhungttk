SELECT TT1.*, TT2.delivered_quantity, TT2.delivered_by, TT2.delivered_by_name, 
	TT3.pr_number, 
    TT3.name as pr_name,
    TT3.description as pr_description,
	TT3.requested_on as pr_requested_on,
	TT3.tItems,
 	TT3.last_status,
    TT3.last_change,
    TT3.department_id,
    TT3.department_name,
    concat(TT3.lastname, ' ', TT3.lastname) as pr_requester
    
FROM  mla_purchase_request_items AS TT1

LEFT JOIN	
    
    (/** Delivered quantity */
    SELECT t2.*, t3.created_on AS delivered_on, SUM(t1.delivered_quantity) AS delivered_quantity, t3.created_by AS delivered_by, t3.delivered_by_name
	FROM mla_delivery_items AS T1
	
    JOIN mla_purchase_request_items AS T2
	ON T2.id = T1.pr_item_id
	
    JOIN 
		(select mla_delivery.*, concat(mla_users.firstname, ' ', mla_users.lastname) as delivered_by_name from mla_delivery 
		join mla_users
		on mla_delivery.created_by = mla_users.id) AS T3
        
	ON T1.delivery_id = t3.id
	
    GROUP BY T1.pr_item_id
    /** ITEMS with Delivered quantity*/) AS TT2
    
ON TT2.id = TT1.id

JOIN

(
	
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
		
        /*Left joint to show all PR*/
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

) 

AS TT3
	
ON TT1.purchase_request_id = TT3.id

ORDER BY TT1.EDT ASC
