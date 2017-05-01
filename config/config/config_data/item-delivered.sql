	/** Delivered quantity */
    SELECT t2.*, t3.created_on AS delivered_on, SUM(t1.delivered_quantity) AS delivered_quantity, t3.created_by AS delivered_by, t3.delivery_by_name
	FROM mla_delivery_items AS T1
	
    JOIN mla_purchase_request_items AS T2
	ON T2.id = T1.pr_item_id
	
    JOIN 
		(select mla_delivery.*, concat(mla_users.firstname, ' ', mla_users.lastname) as delivery_by_name from mla_delivery 
		join mla_users
		on mla_delivery.created_by = mla_users.id) AS T3
        
	ON T1.delivery_id = t3.id
	
    GROUP BY T1.pr_item_id
    /** ITEMS with Delivered quantity*/