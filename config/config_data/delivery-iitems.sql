select * from mla_articles

left join

	(select delivery_items.*, mla_vendors.name as vendor_name

	from  

		(select mla_delivery_items.pr_item_id, mla_delivery_items.vendor_id, mla_delivery_items.price,mla_delivery_items.currency, mla_purchase_request_items.* from mla_delivery_items 
		join mla_purchase_request_items
		on mla_purchase_request_items.id = mla_delivery_items.pr_item_id) as delivery_items

	left join mla_vendors 
	on mla_vendors.id = delivery_items.vendor_id) AS delivery_items1 /* deleivery items with vendor */
    
ON mla_articles.id = delivery_items1.article_id