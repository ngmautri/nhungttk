/*Last DO Item*/
select 
mla_delivery_items_1_1.*,
concat(mla_delivery_items_1_1.article_id, '+++',mla_delivery_items_1_1.last_do_item_created_on) as last_article_do

from 
 
 (select MAX(mla_delivery_items_1.do_item_created_on) AS last_do_item_created_on, 
 mla_delivery_items_1.do_item_id,
 mla_delivery_items_1.article_id,
 	
 mla_vendors.name as vendor_name

	from  

		(select 
			mla_delivery_items.id as do_item_id,
           	mla_delivery_items.created_on as do_item_created_on, 
			mla_delivery_items.pr_item_id, 
			mla_delivery_items.vendor_id, 
			mla_delivery_items.price,
			mla_delivery_items.currency,
    		mla_delivery_items.created_by as do_item_created_by_user_id,
      		mla_purchase_request_items.* 
        from mla_delivery_items 
		join mla_purchase_request_items
		on mla_purchase_request_items.id = mla_delivery_items.pr_item_id) as mla_delivery_items_1 /* DELIVERY - PR*/

	join mla_vendors 
	on mla_vendors.id = mla_delivery_items_1.vendor_id /* DELIVERY - PR - VENDOR*/
    
group by mla_delivery_items_1.article_id) as mla_delivery_items_1_1