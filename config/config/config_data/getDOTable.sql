select
*
from
(
	/*Delivery Items Table*/
	select 
		mla_delivery_items.*,
		concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_key,
		mla_purchase_request_items.article_id,
		concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_key,
		mla_purchase_request_items.sparepart_id,
		concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_key,
		mla_purchase_request_items.asset_id,
		mla_vendors.name as vendor_name
	 
	from mla_delivery_items

	join mla_purchase_request_items
	on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

	join mla_vendors
	on mla_vendors.id = mla_delivery_items.vendor_id
)
As mla_delivery_items_1

LEFT JOIN
(
	/*Last Article DO KEY*/
	select 

		mla_delivery_items_1.article_id,
		max(mla_delivery_items_1.created_on) as article_do_last_change,
		concat(mla_delivery_items_1.article_id,'+++',max(mla_delivery_items_1.created_on)) as article_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_date,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_do_date,
			mla_purchase_request_items.asset_id,
			mla_vendors.name as vendor_name
		 
		from mla_delivery_items

		join mla_purchase_request_items
		on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

		join mla_vendors
		on mla_vendors.id = mla_delivery_items.vendor_id
		/*Delivery Items Table*/

	) as mla_delivery_items_1

	group by mla_delivery_items_1.article_id
	/*Last Article DO KEY*/
) 
AS mla_last_artilce_do

ON mla_last_artilce_do.article_id = mla_delivery_items_1.article_id

LEFT JOIN
(
	/*Last Spartpart DO KEY*/

	select

		mla_delivery_items_1.sparepart_id,
		max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
		concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as spartpart_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as spartpart_do_date,
			mla_purchase_request_items.sparepart_id,
			concat(mla_purchase_request_items.asset_id,'+++',mla_delivery_items.created_on) as asset_last_do,
			mla_purchase_request_items.asset_id,
			mla_vendors.name as vendor_name
		 
		from mla_delivery_items

		join mla_purchase_request_items
		on mla_delivery_items.pr_item_id =  mla_purchase_request_items.id

		join mla_vendors
		on mla_vendors.id = mla_delivery_items.vendor_id
		/*Delivery Items Table*/

	) as mla_delivery_items_1

	group by mla_delivery_items_1.sparepart_id

	/*Last Spartpart DO KEY*/
)
AS mla_last_sparepart_do

ON mla_last_sparepart_do.sparepart_id = mla_delivery_items_1.sparepart_id

/*Delivery Items Table*/
