	
    
    select delivery_items.*, mla_vendors.name as vendor_name, MAX(delivery_items.do_item_created_on) AS last_do_item_created_on

	from  

		(select mla_delivery_items.pr_item_id, 
			mla_delivery_items.vendor_id, 
			mla_delivery_items.price,
			mla_delivery_items.currency,
        	mla_delivery_items.created_on as do_item_created_on, 
			mla_delivery_items.created_by as do_item_created_by_user_id, 
			mla_purchase_request_items.* 
		
        from mla_delivery_items 
		join mla_purchase_request_items
		on mla_purchase_request_items.id = mla_delivery_items.pr_item_id) as delivery_items /* DELIVERY - PR*/

	join mla_vendors 
	on mla_vendors.id = delivery_items.vendor_id /* DELIVERY - PR - VENDOR*/
    
    group by delivery_items.article_id
    
    /* Last DO ITEMS Created ON*/