select 
	mla_delivery.id as dn_id, 
	count(*) as totalItems  
	
    from mla_delivery_items
join mla_delivery 
on mla_delivery_items.delivery_id = mla_delivery.id
group by mla_delivery.id