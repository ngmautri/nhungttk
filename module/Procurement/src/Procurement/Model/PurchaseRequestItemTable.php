<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Procurement\Model\PurchaseRequestItem;

class PurchaseRequestItemTable {
	protected $tableGateway;
	
	private $getPRItems_SQL= "
/* ALL PR ITEMS*/

select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_notified.notified_delivered_quantity,0) as notified_delivered_quantity,
	ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0) as confirmed_delivered_quantity,
			
  if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
		, 0
		,ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance
        
from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

left join
(

		
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/

) 
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
  
/* total notified DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as notified_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'Notified'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* total confirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as confirmed_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'confirmed'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id
)
as mla_purchase_request_items
Where 1

/* ALL PR ITEMS*/			
			
			
			";
	
	private $getPRItems_SQL_V1="

/* ALL PR ITEMS*/

select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_confirmed.confirmed_quantity,0) as confirmed_quantity,
	ifnull(mla_delivery_items_rejected.rejected_quantity,0) as rejected_quantity,
    
    if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_confirmed.confirmed_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance
        
from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

left join
(

		
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/

) 
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* total confirmed DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id

/* total rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_rejected
on mla_delivery_items_rejected.pr_item_id = mla_purchase_request_items.id
)
as mla_purchase_request_items
Where 1

/* ALL PR ITEMS*/			
			
			
			

			";

	private $getPRItems_SQL_V2 ="
/* ALL PR ITEMS*/

select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
   
	ifnull(mla_delivery_items_confirmed.confirmed_quantity,0) as confirmed_quantity,
	ifnull(mla_delivery_items_rejected.rejected_quantity,0) as rejected_quantity,
	
    if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))
    ,0) as confirmed_balance,
	
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_confirmed.confirmed_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
    
    ifnull(mla_delivery_items_notified.unconfimed_quantity,0) as unconfirmed_quantity,
     mla_delivery_cart.status as delivery_cart_status
	
from mla_purchase_request_items
	
/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status
	
from
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on
	
	
from mla_purchase_requests
	
left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id
	
)
as mla_purchase_requests
	
left join
(
	
	
    /**USER-DEPARTMENT beginns*/
    select
        mla_users.title,
        mla_users.firstname,
        mla_users.lastname,
        mla_departments_members_1.*
    from mla_users
    join
	(	select
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
	
)
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
)
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
	
/* total confirmed DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id
	
/* total rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_rejected
on mla_delivery_items_rejected.pr_item_id = mla_purchase_request_items.id

/* total notified /unconfirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as unconfimed_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'Notified'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* Check Delivery Cart */
left join mla_delivery_cart
on mla_delivery_cart.pr_item_id = mla_purchase_request_items.id

Where 1

	
/* ALL PR ITEMS*/	
			";
	
	private $getPRItemsWithDN_SQL= "
			
/* ALL PR ITEMS WITH LAST DN*/
select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_notified.notified_delivered_quantity,0) as notified_delivered_quantity,
	ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0) as confirmed_delivered_quantity,
   	if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
		, 0
		,ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
         
      
    last_sparepart_dn.vendor_name as sp_vendor_name,
   	last_sparepart_dn.vendor_id as sp_vendor_id,
	last_sparepart_dn.price as sp_price,
	last_sparepart_dn.currency as sp_currency,
    
	last_article_dn.vendor_name as article_vendor_name,
   	last_article_dn.vendor_id as article_vendor_id,
	last_article_dn.price as article_price,
	last_article_dn.currency as article_currency

 	from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

left join
(

		
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/

) 
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
  
/* total notified DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as notified_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'Notified'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* total confirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as confirmed_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'confirmed'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id


/* Last Article DN */
left join
(
/* last article dn*/
select
	mla_articles_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_articles_last_dn.article_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as article_dn_last_status,   
			mla_delivery_items_workflows.updated_by as article_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as article_dn_last_status_on

		from mla_articles_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_articles_last_dn.last_workflow_id
	) 
	as mla_articles_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_articles_last_dn.dn_item_id
) 
as mla_articles_last_dn
join mla_vendors

on mla_vendors.id = mla_articles_last_dn.vendor_id 

/* last article dn*/

) 
as last_article_dn
on last_article_dn.article_id = mla_purchase_request_items.article_id

/* Last SP DN */
left join
(
/* last article dn*/
select
	mla_spareparts_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_spareparts_last_dn.sparepart_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as sp_dn_last_status,   
			mla_delivery_items_workflows.updated_by as sp_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as sp_dn_last_status_on

		from mla_spareparts_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_spareparts_last_dn.last_workflow_id
	) 
	as mla_spareparts_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_spareparts_last_dn.dn_item_id
) 
as mla_spareparts_last_dn
join mla_vendors

on mla_vendors.id = mla_spareparts_last_dn.vendor_id 

/* last article dn*/


) 
as last_sparepart_dn
on last_sparepart_dn.sparepart_id = mla_purchase_request_items.sparepart_id
)
as mla_purchase_request_items
where 1


/* ALL PR ITEMS WITH LAST DN*/
			";
	
	private $getPRItemsWithDN_SQL_V1= "
/* ALL PR ITEMS*/

select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_confirmed.confirmed_quantity,0) as confirmed_quantity,
	ifnull(mla_delivery_items_rejected.rejected_quantity,0) as rejected_quantity,
    
    if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_confirmed.confirmed_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
    
	last_sparepart_dn.vendor_name as sp_vendor_name,
   	last_sparepart_dn.vendor_id as sp_vendor_id,
	last_sparepart_dn.price as sp_price,
	last_sparepart_dn.currency as sp_currency,
    
	last_article_dn.vendor_name as article_vendor_name,
   	last_article_dn.vendor_id as article_vendor_id,
	last_article_dn.price as article_price,
	last_article_dn.currency as article_currency

        
from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

left join
(
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/

) 
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* total confirmed DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id

/* total rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_rejected
on mla_delivery_items_rejected.pr_item_id = mla_purchase_request_items.id

/* Last Article DN */
left join
(
/* last article dn*/
select
	mla_articles_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_articles_last_dn.article_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as article_dn_last_status,   
			mla_delivery_items_workflows.updated_by as article_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as article_dn_last_status_on

		from mla_articles_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_articles_last_dn.last_workflow_id
	) 
	as mla_articles_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_articles_last_dn.dn_item_id
) 
as mla_articles_last_dn
join mla_vendors

on mla_vendors.id = mla_articles_last_dn.vendor_id 

/* last article dn*/

) 
as last_article_dn
on last_article_dn.article_id = mla_purchase_request_items.article_id

/* Last SP DN */
left join
(
/* last article dn*/
select
	mla_spareparts_last_dn.*,
	mla_vendors.name as vendor_name
from 
(
	select
	*
	from
	(
		select
			mla_spareparts_last_dn.sparepart_id,
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as sp_dn_last_status,   
			mla_delivery_items_workflows.updated_by as sp_dn_last_status_by,   
			mla_delivery_items_workflows.updated_on as sp_dn_last_status_on

		from mla_spareparts_last_dn
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_spareparts_last_dn.last_workflow_id
	) 
	as mla_spareparts_last_dn

	left join mla_delivery_items
	on mla_delivery_items.id  = mla_spareparts_last_dn.dn_item_id
) 
as mla_spareparts_last_dn
join mla_vendors

on mla_vendors.id = mla_spareparts_last_dn.vendor_id 

/* last article dn*/


) 
as last_sparepart_dn
on last_sparepart_dn.sparepart_id = mla_purchase_request_items.sparepart_id


)
as mla_purchase_request_items
Where 1

/* ALL PR ITEMS*/			
				
			
			";
	
	private $getPRItemsWithDN_SQL_V2="
			
/* ALL PR ITEMS*/

SELECT
	mla_purchase_request_items.*,    
    mla_purchase_requests.pr_number,
    mla_purchase_requests.auto_pr_number AS pr_auto_number,
    mla_purchase_requests.name AS pr_name,
	mla_purchase_requests.description AS pr_description,
    mla_purchase_requests.requested_by AS pr_requested_by,
    mla_purchase_requests.requested_on AS pr_requested_on,
    
    mla_purchase_requests_workflows.status AS pr_last_status,
	mla_purchase_requests_workflows.updated_on AS pr_last_status_on,
	mla_purchase_requests_workflows.updated_by AS pr_last_status_by,
    
 	YEAR(mla_purchase_requests.requested_on) AS pr_year,
    MONTH(mla_purchase_requests.requested_on) AS pr_month,
    
    mla_users.email AS requester_email,
 	CONCAT (mla_users.firstname,' ',mla_users.lastname ) AS pr_requester_name,
    mla_departments.id AS pr_of_department_id,
	mla_departments.name AS pr_of_department,
 	mla_departments.status AS pr_of_department_status,
    
    IFNULL(mla_delivery_items_workflows.total_received_quantity,0) AS total_received_quantity,
    IFNULL(mla_delivery_items_workflows.unconfirmed_quantity,0) AS unconfirmed_quantity,
	IFNULL(mla_delivery_items_workflows.confirmed_quantity,0) AS confirmed_quantity,
	IFNULL(mla_delivery_items_workflows.rejected_quantity,0) AS rejected_quantity,
	
    IF ((mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))
    ,0) AS confirmed_balance,
	
	 IF ((mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0
	, 0
	,IFNULL(mla_delivery_items_workflows.confirmed_quantity,0)-mla_purchase_request_items.quantity) AS confirmed_free_balance,
 	
    mla_spareparts.tag AS sp_tag,     
	
    mla_delivery_items_workflows_article.dn_item_id AS article_last_dn_item_id,
	mla_delivery_items_workflows_article.status AS article_dn_last_status,   
	mla_delivery_items_workflows_article.updated_by AS article_dn_last_status_by,   
	mla_delivery_items_workflows_article.updated_on AS article_dn_last_status_on,
            
    mla_delivery_items_article.vendor_id AS article_vendor_id,
    mla_delivery_items_article.price AS article_price,
    mla_delivery_items_article.currency AS article_currency,

    mla_vendor_article.name AS article_vendor_name,
    
            
	mla_delivery_items_workflows_sp.dn_item_id AS sp_last_dn_item_id,
	mla_delivery_items_workflows_sp.status AS sp_dn_last_status,   
	mla_delivery_items_workflows_sp.updated_by AS sp_dn_last_status_by,   
	mla_delivery_items_workflows_sp.updated_on AS sp_dn_last_status_on,
    
	mla_delivery_items_sp.vendor_id AS sp_vendor_id,
	mla_vendor_sp.name AS sp_vendor_name,
    mla_delivery_items_sp.price AS sp_price,
    mla_delivery_items_sp.currency AS sp_currency,
	
    mla_po_item.id AS po_item_id,
    mla_po_item.vendor_id AS po_vendor_id,
    mla_po_item.price AS po_price,
    mla_po_item.currency AS po_currency,
    mla_po_item.payment_method AS po_payment_method,
    mla_vendors_po.name AS po_vendor_name,
    mla_po_item.remarks AS po_remarks
  
FROM mla_purchase_request_items

/* PURCHASE REQUEST */
LEFT JOIN mla_purchase_requests
ON mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_purchase_requests_workflows
ON mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_users
ON mla_users.id = mla_purchase_requests.requested_by

/* 1 - 1 ONLY*/
LEFT JOIN mla_departments_members
ON mla_users.id = mla_departments_members.user_id

/* 1 - 1 ONLY*/
LEFT JOIN mla_departments
ON mla_departments_members.department_id = mla_departments.id
/* PURCHASE REQUEST */	
	
/* total confirmed, rejected and unconfirmed DN */
LEFT JOIN
(
	SELECT
		mla_delivery_items.pr_item_id,
		mla_delivery_items.po_item_id,
        mla_delivery_items_workflows.status AS last_dn_status,
		
		SUM(mla_delivery_items.delivered_quantity) AS total_received_quantity,
        IFNULL(SUM(CASE WHEN mla_delivery_items_workflows.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END),0) AS unconfirmed_quantity,
		IFNULL(SUM(mla_delivery_items_workflows.confirmed_quantity),0) AS confirmed_quantity,
		IFNULL(SUM(mla_delivery_items_workflows.rejected_quantity),0) AS rejected_quantity
	FROM mla_delivery_items
    
    LEFT JOIN mla_delivery_items_workflows
    ON mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
    
	GROUP BY mla_delivery_items.pr_item_id
)
AS mla_delivery_items_workflows
ON mla_delivery_items_workflows.pr_item_id = mla_purchase_request_items.id
	
/* Last Article DN */
LEFT JOIN mla_articles_last_dn
ON mla_articles_last_dn.article_id = mla_purchase_request_items.article_id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_article
ON mla_delivery_items_workflows_article.id = mla_articles_last_dn.last_workflow_id
		
LEFT JOIN mla_delivery_items AS mla_delivery_items_article
ON mla_delivery_items_article.id = mla_delivery_items_workflows_article.dn_item_id
       
LEFT JOIN mla_vendors AS mla_vendor_article
ON mla_delivery_items_article.vendor_id = mla_vendor_article.id
/* Last Article DN */


/* Last SP DN */
LEFT JOIN mla_spareparts_last_dn
ON mla_spareparts_last_dn.sparepart_id = mla_purchase_request_items.sparepart_id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_sp
ON mla_delivery_items_workflows_sp.id = mla_spareparts_last_dn.last_workflow_id
		
LEFT JOIN mla_delivery_items AS mla_delivery_items_sp
ON mla_delivery_items_sp.id = mla_delivery_items_workflows_sp.dn_item_id
        
LEFT JOIN mla_vendors AS mla_vendor_sp
ON mla_delivery_items_sp.vendor_id = mla_vendor_sp.id
/* Last SP DN */
 
/* SP */
LEFT JOIN mla_spareparts
ON mla_spareparts.id = mla_purchase_request_items.sparepart_id

/* PO ITEMS 1-1*/
LEFT JOIN mla_po_item
ON mla_po_item.pr_item_id = mla_purchase_request_items.id

LEFT JOIN mla_vendors AS mla_vendors_po
ON mla_vendors_po.id = mla_po_item.vendor_id
/* PO ITEMS 1-1*/

WHERE 1
AND mla_purchase_requests_workflows.status IS NOT NULL
/* ALL PR ITEMS*/
			
			
				
	";
	
	private $getPRItemsToDeliver_SQL ="
/* ALL PR ITEMS*/

select
*
from
(
select
	mla_purchase_request_items.*,
	mla_purchase_requests.pr_number,
    mla_purchase_requests.name as pr_name,
	mla_purchase_requests.description as pr_description,
    mla_purchase_requests.requested_by as pr_requested_by,
    mla_purchase_requests.requested_on as pr_requested_on,
    mla_purchase_requests.pr_last_status,
	mla_purchase_requests.pr_last_status_on,
	mla_purchase_requests.pr_last_status_by,
 	mla_purchase_requests.pr_year,
 	mla_purchase_requests.pr_requester_name,
	
    mla_purchase_requests.pr_of_department_id,
	mla_purchase_requests.pr_of_department,
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_notified.notified_delivered_quantity,0) as notified_delivered_quantity,
	ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0) as confirmed_delivered_quantity,
			
  if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0))>=0
		, 0
		,ifnull(mla_delivery_items_confirmed.confirmed_delivered_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
        mla_delivery_cart.status as delivery_cart_status
        
from mla_purchase_request_items

/* purchase requests*/
left join
(
select
	mla_purchase_requests.*,
	year(mla_purchase_requests.requested_on) as pr_year,
	
    concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
	mla_users.department_id as pr_of_department_id,
	mla_users.department_name as pr_of_department,
	mla_users.department_status as pr_of_department_status

from 
(
select
	mla_purchase_requests.*,
	mla_purchase_requests_workflows.status as pr_last_status,
    mla_purchase_requests_workflows.updated_by as pr_last_status_by,
	mla_purchase_requests_workflows.updated_on as pr_last_status_on

    
from mla_purchase_requests

left join mla_purchase_requests_workflows
	on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

)
as mla_purchase_requests

left join
(

		
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/

) 
as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by
) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
  
/* total notified DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as notified_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'Notified'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_notified
on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

/* total confirmed DN */
left join
(
	select
		mla_delivery_items.*,
		sum(mla_delivery_items.delivered_quantity) as confirmed_delivered_quantity
	from
	(
		select 
			mla_delivery_items.*,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
		)
	as mla_delivery_items
	where mla_delivery_items.dn_last_status = 'confirmed'
	group by mla_delivery_items.pr_item_id
)
as mla_delivery_items_confirmed
on mla_delivery_items_confirmed.pr_item_id = mla_purchase_request_items.id

/* Check Delivery Cart */
left join mla_delivery_cart
on mla_delivery_cart.pr_item_id = mla_purchase_request_items.id

)
as mla_purchase_request_items
Where 1

AND mla_purchase_request_items.confirmed_balance >0

/* ALL PR ITEMS*/	
	
	";
	
	
	
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $article_id        	
	 * @param unknown $sparepart_id        	
	 */
	public function getLastDO($article_id, $sparepart_id) {

		$adapter = $this->tableGateway->adapter;
		$sql = "

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

SELECT 
	mla_delivery_items_2.*
FROM
(

	/* DELIVERY ITEMS TABLE WITH LAST DO*/
	select
		mla_delivery_items_1.*,
		mla_last_article_do.article_do_last_change,
		mla_last_sparepart_do.sparepart_do_last_change
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

	left JOIN
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
	AS mla_last_article_do

	ON mla_last_article_do.article_last_do_key = mla_delivery_items_1.article_do_key

	 left JOIN
	(
	/*Last Spartpart DO KEY*/

	select

		mla_delivery_items_1.sparepart_id,
		max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
		concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as sparepart_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as sparepart_do_date,
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

	ON mla_last_sparepart_do.sparepart_last_do_key = mla_delivery_items_1.spartpart_do_key
	/* DELIVERY ITEMS TABLE WITH LAST DO*/
)

AS mla_delivery_items_2

WHERE mla_delivery_items_2.article_do_last_change  IS NOT NULL OR mla_delivery_items_2.sparepart_do_last_change IS NOT NULL

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

";
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
	public function get($id) {
		$id = ( int ) $id;
		
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/**
	 *
	 * public $id;
	 * public $purchase_request_id;
	 * public $priority;
	 * public $name;
	 * public $description;
	 *
	 * public $code;
	 * public $keywords;
	 *
	 * public $unit;
	 * public $quantity;
	 * public $EDT;
	 *
	 * public $article_id;
	 * public $sparepart_id;
	 * public $asset_id;
	 * public $other_res_id;
	 *
	 * public $remarks;
	 * public $created_on;
	 * 
	 * @param PurchaseRequestItem $input        	
	 * @return number
	 */
	public function add(PurchaseRequestItem $input) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				
				'code' => $input->code,
				'keywords' => $input->keywords,
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				
				'article_id' => $input->article_id,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
				
				'asset_name' => $input->asset_name,				
				'purpose' => $input->purpose,
				'remarks' => $input->remarks,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by 
		)
		;
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param PurchaseRequestItem $input
	 * @param unknown $id
	 */
	public function update(PurchaseRequestItem $input, $id) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				
				'code' => $input->code,
				'keywords' => $input->keywords,
				
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				
				'article_id' => $input->article_id,
				'sparepart_id' => $input->sparepart_id,
				'asset_id' => $input->asset_id,
				'other_res_id' => $input->other_res_id,
				
				'asset_name' => $input->asset_name,				
				'purpose' => $input->purpose,
				'remarks' => $input->remarks,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by 
		)
		;
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	/*
	 * GET ITEM
	 */
	public function getItem($id) {
		$adapter = $this->tableGateway->adapter;
		
		/*
		 * $sql = "SELECT *
		 * FROM mla_purchase_request_items
		 * WHERE purchase_request_id = ". $pr .
		 * " ORDER BY EDT ASC";
		 */
		
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id
	
left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id
	
WHERE TT1.id = " . $id . " ORDER BY TT1.EDT ASC";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet->current ();
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function getPRItem($id) {
		
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
		
		$sql = $sql. " AND mla_purchase_request_items.id = ".$id;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if($resultSet->count() == 1):
			return $resultSet->current();
		else:
			return null;
		endif;
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRItemsToDeliver($department,$limit,$offset){
		
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsToDeliver_SQL;
		
		$sql = $sql. " AND mla_purchase_request_items.pr_last_status IS NOT NULL";
		$sql = $sql. " AND mla_purchase_request_items.delivery_cart_status IS NULL";
		
		if ($department > 0) {
			$sql = $sql. " AND mla_purchase_request_items.pr_of_department_id = " . $department;
		}
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		//echo ($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
		
	}
	
	
	
	/**
	 * 
	 * @param unknown $pr
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getItemsByPR($pr) {
		$adapter = $this->tableGateway->adapter;
		
		/*
		 * $sql = "SELECT *
		 * FROM mla_purchase_request_items
		 * WHERE purchase_request_id = ". $pr .
		 * " ORDER BY EDT ASC";
		 */
		
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by 
from mla_delivery_items as T1
left join mla_purchase_request_items as T2 
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id

left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id
				
WHERE TT1.purchase_request_id = " . $pr . " ORDER BY TT1.EDT ASC";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $pr
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getItemsByPR2($pr_id) {
		
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItems_SQL_V2;
		
		$sql = $sql . " AND mla_purchase_request_items.purchase_request_id = ". $pr_id;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $pr
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getItemsByPR3($pr_id) {
	
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
	
		$sql = $sql . " AND mla_purchase_request_items.purchase_request_id = ". $pr_id;
		
		$sql = $sql .";";
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 *
	 * @param unknown $pr
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPOGrandTotal($pr_id) {
	
		$adapter = $this->tableGateway->adapter;
		$sql = "
/* PO ITEMS 1-1*/
SELECT
	mla_purchase_request_items.purchase_request_id,
		SUM(mla_po_item.price*mla_purchase_request_items.quantity) AS grand_total,
	mla_po_item.currency
FROM mla_purchase_request_items
LEFT JOIN mla_po_item
ON mla_po_item.pr_item_id = mla_purchase_request_items.id
WHERE mla_purchase_request_items.purchase_request_id = " . $pr_id; 
				
		$sql = $sql. " GROUP BY mla_po_item.currency";
	
		
		$sql = $sql .";";
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getItems() {
		$adapter = $this->tableGateway->adapter;
		$sql = "
select TT3.pr_number, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1
left join
(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
from mla_delivery_items as T1
left join mla_purchase_request_items as T2
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
group by T1.pr_item_id) as TT2
On TT2.id = TT1.id
	
left join mla_purchase_requests as TT3
on TT1.purchase_request_id = TT3.id";
		
		// "ORDER BY TT1.EDT ASC";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $department
	 * @param unknown $last_status
	 * @param unknown $balance
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRItemsWithLastDN($department,$last_status,$balance,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL;
		
		
		$sql = $sql. " AND mla_purchase_request_items.pr_last_status IS NOT NULL";
		
		if ($department > 0) {
			$sql = $sql. " AND mla_purchase_request_items.pr_of_department_id = " . $department;
		}
		
		if ($last_status != "" || $last_status !=null) {
				$sql = $sql. " AND mla_purchase_request_items.pr_last_status = '" . $last_status . "'";
		}
		
		if ($balance == 0) {
			$sql = $sql. " AND mla_purchase_request_items.confirmed_balance = 0";
		}
		if ($balance == 1) {
			$sql = $sql. " AND mla_purchase_request_items.confirmed_balance > 0";
		}

		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		//echo ($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $department
	 * @param unknown $last_status
	 * @param unknown $balance
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRItemsWithLastDN_V1($department,$last_status,$balance,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V1;
	
	
		$sql = $sql. " AND mla_purchase_request_items.pr_last_status IS NOT NULL";
	
		if ($department > 0) {
			$sql = $sql. " AND mla_purchase_request_items.pr_of_department_id = " . $department;
		}
	
		if ($last_status != "" || $last_status !=null) {
			$sql = $sql. " AND mla_purchase_request_items.pr_last_status = '" . $last_status . "'";
		}
	
		if ($balance == 0) {
			$sql = $sql. " AND mla_purchase_request_items.confirmed_balance = 0";
		}
		if ($balance == 1) {
			$sql = $sql. " AND mla_purchase_request_items.confirmed_balance > 0";
		}
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $department
	 * @param unknown $last_status
	 * @param unknown $balance
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRItemsWithLastDN_V2($pr_year,$department,$last_status,$balance,$unconfirmed_quantity,$processing,$sort_by,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
	
		if ($pr_year > 0) {
			$sql = $sql . " AND year(mla_purchase_requests.requested_on)=" . $pr_year;
		}
		
		
		if ($department > 0) {
			$sql = $sql. " AND mla_departments.id = " . $department;
		}
	
		if ($last_status != "" || $last_status !=null) {
			$sql = $sql. " AND mla_purchase_requests_workflows.status= '" . $last_status . "'";
		}
	
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) < 0";
		}
		
		// unconfirmed
		if ($unconfirmed_quantity == 0) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_workflows.unconfimed_quantity,0)) = 0";
		}
		if ($unconfirmed_quantity ==1) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_workflows.unconfimed_quantity,0)) > 0";
		}
		
		// added into po_items?
		if ($processing == 0) {
			$sql = $sql. " AND mla_po_item.id IS NULL";
		}
		if ($processing ==1) {
			$sql = $sql. " AND mla_po_item.id >0";
		}
		
		
		if ($sort_by =="pr_number") {
			$sql = $sql. " order by mla_purchase_request_items.purchase_request_id desc";
		}
			
		if ($sort_by == "item_name") {
			$sql = $sql. " order by mla_purchase_request_items.name asc";
		}
		
		if ($sort_by =="requester_name") {
			$sql = $sql. " order by mla_users.lastname asc";
		}
		
		if ($sort_by =="EDT") {
			$sql = $sql. " order by mla_purchase_request_items.edt asc";
		}
		
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		$sql = $sql.";";
			
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $department
	 * @param unknown $balance
	 * @param unknown $payment_method
	 * @param unknown $currency
	 * @param unknown $vendor_id
	 * @param unknown $sort_by
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPOItems($department,$balance,$payment_method,$currency,$vendor_id,$sort_by,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
	
		$sql = $sql. " AND mla_po_item.id > 0";
		
		
		if ($department > 0) {
			$sql = $sql. " AND mla_departments.id = " . $department;
		}
	
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) < 0";
		}
	
		if ($vendor_id > 0) {
			$sql = $sql. " AND mla_po_item.vendor_id = " . $vendor_id;
		}
		
	
		if ($sort_by =="pr_number") {
			$sql = $sql. " order by mla_purchase_request_items.purchase_request_id desc";
		}
		
		if ($payment_method != null or $payment_method != '') {
			$sql = $sql. " AND mla_po_item.payment_method='".$payment_method."'";
		}
		
		if ($currency != null or $currency != '') {
			$sql = $sql. " AND mla_po_item.currency='".$currency."'";
		}
			
		if ($sort_by == "item_name") {
			$sql = $sql. " order by mla_purchase_request_items.name asc";
		}
	
		if ($sort_by =="requester_name") {
			$sql = $sql. " order by mla_users.lastname asc";
		}
	
		if ($sort_by =="EDT") {
			$sql = $sql. " order by mla_purchase_request_items.edt asc";
		}
	
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		$sql = $sql.";";
			
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * @param unknown $last_status
	 * @param unknown $balance
	 * @param unknown $unconfirmed_quantity
	 * @param unknown $processing
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPRItemsOf($user_id,$pr_year,$last_status,$balance,$unconfirmed_quantity,$processing,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
	
		
		if ($user_id > 0) {
			$sql = $sql. " AND mla_purchase_requests.requested_by= " . $user_id;
		}
		
		if ($pr_year > 0) {
			$sql = $sql . " AND year(mla_purchase_requests.requested_on)=" . $pr_year;
		}
		
	
		if ($last_status != "" || $last_status !=null) {
			$sql = $sql. " AND mla_purchase_requests_workflows.status = '" . $last_status . "'";
		}
	
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) < 0";
		}
	
		// unconfirmed
		if ($unconfirmed_quantity == 0) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_workflows.unconfimed_quantity,0)) = 0";
		}
		if ($unconfirmed_quantity ==1) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_workflows.unconfimed_quantity,0)) > 0";
		}
	
		// added into po_items?
		if ($processing == 0) {
			$sql = $sql. " AND mla_po_item.id IS NULL";
		}
		if ($processing ==1) {
			$sql = $sql. " AND mla_po_item.id >0";
		}
	
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
/**
 * 
 * @param unknown $pr_id
 * @param unknown $balance
 * @param unknown $unconfirmed_quantity
 * @param unknown $processing
 * @param unknown $limit
 * @param unknown $offset
 * @return \Zend\Db\ResultSet\ResultSet
 */
	public function getPRItemsWithLastDN_V3($pr_id,$balance,$unconfirmed_quantity,$processing,$limit,$offset) {
			
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
	
	
		$sql = $sql. " AND mla_purchase_requests_workflows.status IS NOT NULL";
	
		if ($pr_id > 0) {
			$sql = $sql. " AND mla_purchase_request_items.purchase_request_id = " . $pr_id;
		}
	
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) < 0";
		}
	
		// unconfirmed
		if ($unconfirmed_quantity == 0) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_notified.unconfimed_quantity,0)) = 0";
		}
		if ($unconfirmed_quantity ==1) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_notified.unconfimed_quantity,0)) > 0";
		}
	
		// added into po_items?
		if ($processing == 0) {
			$sql = $sql. " AND mla_po_item.id IS NULL";
		}
		if ($processing ==1) {
			$sql = $sql. " AND mla_po_item.id >0";
		}
	
		
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		$sql = $sql.";";
		
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * =====================
	 */
	public function getSubmittedPRItems() {
		$adapter = $this->tableGateway->adapter;
		$sql = "
				
SElECT 
	mla_purchase_request_items.*,
 	mla_delivery_items_1.delivered_quantity,
    
	mla_purchase_requests_1.last_pr_status,
	mla_purchase_requests_1.last_pr_status_on,
  	mla_purchase_requests_1.department_id,
 	mla_purchase_requests_1.requested_by,
 	
    mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
	mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
 
FROM mla_purchase_request_items

LEFT JOIN
(
	/*Delivered Quantity*/
	select 
		mla_purchase_request_items.id as pr_item_id, 
	   sum(mla_delivery_items.delivered_quantity) as delivered_quantity
		from mla_delivery_items

	left join mla_purchase_request_items
	On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

	left join mla_delivery
	on mla_delivery_items.delivery_id = mla_delivery.id

	group by mla_delivery_items.pr_item_id
	/*Delivered Quantity*/

) AS mla_delivery_items_1

ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

/* JOIN: select only submitted PR */
JOIN 
(
	/* PR: with total items, last status, user and department*/

	SELECT
		mla_purchase_requests.*,
		mla_purchase_request_items_1.tItems as totalItems,
		mla_purchase_requests_workflows_1_1.status as last_pr_status,
		mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
		mla_users_1.*

	FROM mla_purchase_requests

	LEFT JOIN
	(
		/* TOTAL PR Items*/
		SELECT
			mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
		FROM
			mla_purchase_request_items
		JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
		GROUP BY mla_purchase_requests.id
		/* TOTAL PR Items*/

	) AS mla_purchase_request_items_1
	ON mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

	JOIN
	(
			/* Last Workflow changed PR) */
			select
				mla_purchase_requests_workflows_1.*
			from 

				(select 
				mla_purchase_requests_workflows.*,
				concat(mla_purchase_requests_workflows.purchase_request_id,'+++',mla_purchase_requests_workflows.updated_on) as pr_id_changed_on
				from mla_purchase_requests_workflows) as mla_purchase_requests_workflows_1

			join

				(select 
					max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
					concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
					mla_purchase_requests_workflows.purchase_request_id
					 from mla_purchase_requests_workflows
				group by mla_purchase_requests_workflows.purchase_request_id) AS mla_purchase_requests_workflows_2

			on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
            
            /* FILTER  
            WHERE mla_purchase_requests_workflows_1.status='Approved'*/
           
            
            
			/* Last Workflow changed PR) */
		) AS mla_purchase_requests_workflows_1_1

		ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

		JOIN
		(
			/**USER-DEPARTMENT beginns*/
			SELECT 
				mla_users.title, 
				mla_users.firstname, 
				mla_users.lastname, 
				mla_departments_members_1.*
			FROM mla_users
			JOIN 
			(	SELECT 
					mla_departments_members.department_id,
					mla_departments_members.user_id,
					mla_departments.name AS department_name,
					mla_departments.status AS department_status
				FROM mla_departments_members
				JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
                
                /* Filter*/
                WHERE mla_departments_members.department_id = 2
                
			) AS mla_departments_members_1 
			ON mla_users.id = mla_departments_members_1.user_id
			/**USER-DEPARTMENT ends*/
            
		) AS mla_users_1
		ON mla_users_1.user_id = mla_purchase_requests.requested_by

) AS mla_purchase_requests_1

ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

LEFT JOIN
(
	/* Last Workflow changed PR ITEM) */
	select
		mla_purchase_requests_items_workflows_1.*
	from 

		(select 
		mla_purchase_requests_items_workflows.*,
		concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
		from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

	join

		(select 
			max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
			mla_purchase_requests_items_workflows.pr_item_id
			 from mla_purchase_requests_items_workflows
		group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

	on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
	/* Last Workflow changed PR ITEM) */

) AS mla_purchase_requests_items_workflows_1_1

ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

LEFT JOIN
(
	  /**USER-DEPARTMENT beginns*/
    SELECT 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    FROM mla_users
    JOIN 
	(	SELECT 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name AS department_name,
            mla_departments.status AS department_status
		FROM mla_departments_members
		JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
	) AS mla_departments_members_1 
    ON mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
) as mla_users_1

ON mla_users_1.user_id = mla_purchase_request_items.created_by
				
ORDER BY mla_purchase_request_items.EDT ASC
		";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 * Get all submitted PR ITEM, with filter
	 */
	public function getAllSubmittedPRItems($last_status, $user_id, $department_id, $balance, $limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		
		if ($balance == - 1) {
			$t_balance = ' 1';
		}
		
		if ($balance == 0) {
			$t_balance = ' mla_purchase_request_items_1.balance = 0';
		}
		if ($balance == 1) {
			$t_balance = ' mla_purchase_request_items_1.balance >0';
		}
		
		if ($limit == 0) {
			$t_limit = '';
		} else {
			$t_limit = ' LIMIT ' . $limit;
		}
		
		if ($offset == 0) {
			$t_offset = '';
		} else {
			$t_offset = ' OFFSET ' . $offset;
		}
		
		if ($last_status == '') {
			$last_status_filter = 'WHERE 1';
		} else {
			$last_status_filter = "WHERE mla_purchase_requests_workflows_1.status = '" . $last_status . "'";
		}
		
		if ($user_id === '') {
			if ($department_id === '') {
				$filter = '';
			} else {
				$filter = 'WHERE mla_departments_members.department_id =' . $department_id;
			}
		} else {
			$filter = 'WHERE mla_departments_members.user_id =' . $user_id;
			if ($department_id != '') {
				$filter = $filter . ' AND mla_departments_members.department_id =' . $department_id;
			}
		}
		
		$sql = "
SELECT 
*
FROM 
(
	SElECT 
		mla_purchase_request_items.*,
		ifnull(mla_delivery_items_1.delivered_quantity,0) as delivered_quantity,
		(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_1.delivered_quantity,0)) as balance,
		mla_purchase_requests_1.pr_number,
		mla_purchase_requests_1.last_pr_status,
		mla_purchase_requests_1.last_pr_status_on,
		mla_purchase_requests_1.department_id,
		mla_purchase_requests_1.department_name,
		mla_purchase_requests_1.requested_by,
	 
		
		mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
		mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
	 
	FROM mla_purchase_request_items

	LEFT JOIN
	(
		/*Delivered Quantity*/
		select 
			mla_purchase_request_items.id as pr_item_id, 
			sum(mla_delivery_items.delivered_quantity) as delivered_quantity
			from mla_delivery_items

		left join mla_purchase_request_items
		On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

		left join mla_delivery
		on mla_delivery_items.delivery_id = mla_delivery.id

		group by mla_delivery_items.pr_item_id
		/*Delivered Quantity*/
	) AS mla_delivery_items_1

	ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

	/* JOIN: select only submitted PR */
	JOIN 
	(
		/* PR: with total items, last status, user and department*/

		SELECT
			mla_purchase_requests.*,
			mla_purchase_request_items_1.tItems as totalItems,
			mla_purchase_requests_workflows_1_1.status as last_pr_status,
			mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
			mla_users_1.*

		FROM mla_purchase_requests

		LEFT JOIN
		(
			/* TOTAL PR Items*/
			SELECT
				mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
			FROM
				mla_purchase_request_items
			JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
			GROUP BY mla_purchase_requests.id
			/* TOTAL PR Items*/

		) AS mla_purchase_request_items_1
		ON mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

		JOIN
		(
				/* Last Workflow changed PR) */
				select
					mla_purchase_requests_workflows_1.*
				from 

					(select 
					mla_purchase_requests_workflows.*,
					concat(mla_purchase_requests_workflows.purchase_request_id,'+++',mla_purchase_requests_workflows.updated_on) as pr_id_changed_on
					from mla_purchase_requests_workflows) as mla_purchase_requests_workflows_1

				join

					(select 
						max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
						concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
						mla_purchase_requests_workflows.purchase_request_id
						 from mla_purchase_requests_workflows
					group by mla_purchase_requests_workflows.purchase_request_id) AS mla_purchase_requests_workflows_2

				on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
				
				/* FILTER  
				WHERE mla_purchase_requests_workflows_1.status='Approved'*/
			    " . $last_status_filter . "
				
				/* Last Workflow changed PR) */
			) AS mla_purchase_requests_workflows_1_1

			ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

			JOIN
			(
				/**USER-DEPARTMENT beginns*/
				SELECT 
					mla_users.title, 
					mla_users.firstname, 
					mla_users.lastname, 
					mla_departments_members_1.*
				FROM mla_users
				JOIN 
				(	SELECT 
						mla_departments_members.department_id,
						mla_departments_members.user_id,
						mla_departments.name AS department_name,
						mla_departments.status AS department_status
					FROM mla_departments_members
					JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
					
					/* Filter*/
					 " . $filter . "
					
				) AS mla_departments_members_1 
				ON mla_users.id = mla_departments_members_1.user_id
				/**USER-DEPARTMENT ends*/
				
			) AS mla_users_1
			ON mla_users_1.user_id = mla_purchase_requests.requested_by

	) AS mla_purchase_requests_1

	ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

	LEFT JOIN
	(
		/* Last Workflow changed PR ITEM) */
		select
			mla_purchase_requests_items_workflows_1.*
		from 

			(select 
			mla_purchase_requests_items_workflows.*,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
			from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

		join

			(select 
				max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
				concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
				mla_purchase_requests_items_workflows.pr_item_id
				 from mla_purchase_requests_items_workflows
			group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

		on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		 group by mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		/* Last Workflow changed PR ITEM) */

	) AS mla_purchase_requests_items_workflows_1_1

	ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

	LEFT JOIN
	(
		  /**USER-DEPARTMENT beginns*/
		SELECT 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname, 
			mla_departments_members_1.*
		FROM mla_users
		JOIN 
		(	SELECT 
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name AS department_name,
				mla_departments.status AS department_status
			FROM mla_departments_members
			JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
		) AS mla_departments_members_1 
		ON mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/
	) as mla_users_1

	ON mla_users_1.user_id = mla_purchase_request_items.created_by
) AS mla_purchase_request_items_1

WHERE " . $t_balance . " ORDER BY mla_purchase_request_items_1.EDT ASC
" . $t_limit . $t_offset;
		
		// echo($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $last_status
	 * @param unknown $user_id
	 * @param unknown $department_id
	 * @param unknown $balance
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getAllSubmittedPRItemsWithLastDO($last_status, $user_id, $department_id, $balance, $limit, $offset) {
		$adapter = $this->tableGateway->adapter;
	
		if ($balance == - 1) {
			$t_balance = ' 1';
		}
	
		if ($balance == 0) {
			$t_balance = ' mla_purchase_request_items_1.balance = 0';
		}
		if ($balance == 1) {
			$t_balance = ' mla_purchase_request_items_1.balance >0';
		}
	
		if ($limit == 0) {
			$t_limit = '';
		} else {
			$t_limit = ' LIMIT ' . $limit;
		}
	
		if ($offset == 0) {
			$t_offset = '';
		} else {
			$t_offset = ' OFFSET ' . $offset;
		}
	
		if ($last_status == '') {
			$last_status_filter = 'WHERE 1';
		} else {
			$last_status_filter = "WHERE mla_purchase_requests_workflows_1.status = '" . $last_status . "'";
		}
	
		if ($user_id === '') {
			if ($department_id === '') {
				$filter = '';
			} else {
				$filter = 'WHERE mla_departments_members.department_id =' . $department_id;
			}
		} else {
			$filter = 'WHERE mla_departments_members.user_id =' . $user_id;
			if ($department_id != '') {
				$filter = $filter . ' AND mla_departments_members.department_id =' . $department_id;
			}
		}
	
		$sql = "
SELECT 
mla_purchase_request_items_1.*,

mla_delivery_items_4.vendor_name as sp_vendor_name,
mla_delivery_items_3.vendor_name as article_vendor_name,
mla_delivery_items_4.price as sp_price,
mla_delivery_items_3.price as article_price,
mla_delivery_items_4.vendor_id as sp_vendor_id,
mla_delivery_items_3.vendor_id as article_vendor_id,
mla_delivery_items_4.currency as sp_currency,
mla_delivery_items_3.currency as article_currency
FROM 
(

	SElECT 
		mla_purchase_request_items.*,
		ifnull(mla_delivery_items_1.delivered_quantity,0) as delivered_quantity,
		(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_1.delivered_quantity,0)) as balance,
		mla_purchase_requests_1.pr_number,
        year(mla_purchase_requests_1.requested_on) as pr_year,
          
		mla_purchase_requests_1.last_pr_status,
		mla_purchase_requests_1.last_pr_status_on,
		mla_purchase_requests_1.department_id,
		mla_purchase_requests_1.department_name,
		mla_purchase_requests_1.requested_by,
	 
		
		mla_purchase_requests_items_workflows_1_1.status AS last_pr_item_status,
		mla_purchase_requests_items_workflows_1_1.updated_on AS last_pr_item_status_on
	 
	FROM mla_purchase_request_items

	LEFT JOIN
	(
		/*Delivered Quantity*/
		select 
			mla_purchase_request_items.id as pr_item_id, 
			sum(mla_delivery_items.delivered_quantity) as delivered_quantity
			from mla_delivery_items

		left join mla_purchase_request_items
		On mla_purchase_request_items.id = mla_delivery_items.pr_item_id

		left join mla_delivery
		on mla_delivery_items.delivery_id = mla_delivery.id

		group by mla_delivery_items.pr_item_id
		/*Delivered Quantity*/
	) AS mla_delivery_items_1

	ON mla_purchase_request_items.id = mla_delivery_items_1.pr_item_id

	/* JOIN: select only submitted PR */
	JOIN 
	(
		/* PR: with total items, last status, user and department*/

		SELECT
			mla_purchase_requests.*,
			mla_purchase_request_items_1.tItems as totalItems,
			mla_purchase_requests_workflows_1_1.status as last_pr_status,
			mla_purchase_requests_workflows_1_1.updated_on as last_pr_status_on,
			mla_users_1.*

		FROM mla_purchase_requests

		LEFT JOIN
		(
			/* TOTAL PR Items*/
			SELECT
				mla_purchase_requests.id AS purchase_request_id, COUNT(*) AS tItems
			FROM
				mla_purchase_request_items
			JOIN mla_purchase_requests ON mla_purchase_request_items.purchase_request_id = mla_purchase_requests.id
			GROUP BY mla_purchase_requests.id
			/* TOTAL PR Items*/

		) AS mla_purchase_request_items_1
		ON mla_purchase_requests.id = mla_purchase_request_items_1.purchase_request_id

		JOIN
		(
				/* Last Workflow changed PR) */
				select
					mla_purchase_requests_workflows_1.*
				from 

					(select 
					mla_purchase_requests_workflows.*,
					concat(mla_purchase_requests_workflows.purchase_request_id,'+++',mla_purchase_requests_workflows.updated_on) as pr_id_changed_on
					from mla_purchase_requests_workflows) as mla_purchase_requests_workflows_1

				join

					(select 
						max(mla_purchase_requests_workflows.updated_on) AS pr_last_change,
						concat(mla_purchase_requests_workflows.purchase_request_id,'+++',max(mla_purchase_requests_workflows.updated_on)) as pr_id_lastchange_on,
						mla_purchase_requests_workflows.purchase_request_id
						 from mla_purchase_requests_workflows
					group by mla_purchase_requests_workflows.purchase_request_id) AS mla_purchase_requests_workflows_2

				on mla_purchase_requests_workflows_2.pr_id_lastchange_on = mla_purchase_requests_workflows_1.pr_id_changed_on
				
				/* FILTER  
				WHERE mla_purchase_requests_workflows_1.status='Approved'*/
			   " . $last_status_filter . "
				
				/* Last Workflow changed PR) */
			) AS mla_purchase_requests_workflows_1_1

			ON mla_purchase_requests.id  = mla_purchase_requests_workflows_1_1.purchase_request_id

			JOIN
			(
				/**USER-DEPARTMENT beginns*/
				SELECT 
					mla_users.title, 
					mla_users.firstname, 
					mla_users.lastname, 
					mla_departments_members_1.*
				FROM mla_users
				JOIN 
				(	SELECT 
						mla_departments_members.department_id,
						mla_departments_members.user_id,
						mla_departments.name AS department_name,
						mla_departments.status AS department_status
					FROM mla_departments_members
					JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
					
					/* Filter*/
				
					" . $filter . "
							
				) AS mla_departments_members_1 
				ON mla_users.id = mla_departments_members_1.user_id
				/**USER-DEPARTMENT ends*/
				
			) AS mla_users_1
			ON mla_users_1.user_id = mla_purchase_requests.requested_by

	) AS mla_purchase_requests_1

	ON mla_purchase_requests_1.id = mla_purchase_request_items.purchase_request_id

	LEFT JOIN
	(
		/* Last Workflow changed PR ITEM) */
		select
			mla_purchase_requests_items_workflows_1.*
		from 

			(select 
			mla_purchase_requests_items_workflows.*,
			concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
			from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

		join

			(select 
				max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
				concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
				mla_purchase_requests_items_workflows.pr_item_id
				 from mla_purchase_requests_items_workflows
			group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

		on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		 group by mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
		/* Last Workflow changed PR ITEM) */

	) AS mla_purchase_requests_items_workflows_1_1

	ON mla_purchase_requests_items_workflows_1_1.pr_item_id = mla_purchase_request_items.id

	LEFT JOIN
	(
		  /**USER-DEPARTMENT beginns*/
		SELECT 
			mla_users.title, 
			mla_users.firstname, 
			mla_users.lastname, 
			mla_departments_members_1.*
		FROM mla_users
		JOIN 
		(	SELECT 
				mla_departments_members.department_id,
				mla_departments_members.user_id,
				mla_departments.name AS department_name,
				mla_departments.status AS department_status
			FROM mla_departments_members
			JOIN mla_departments ON mla_departments_members.department_id = mla_departments.id
		) AS mla_departments_members_1 
		ON mla_users.id = mla_departments_members_1.user_id
		/**USER-DEPARTMENT ends*/
	) as mla_users_1

	ON mla_users_1.user_id = mla_purchase_request_items.created_by
)  

AS mla_purchase_request_items_1

/* LAST ARTICLE DO */
left JOIN
(
/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

SELECT 
	mla_delivery_items_2.*
FROM
(

	/* DELIVERY ITEMS TABLE WITH LAST DO*/
	select
		mla_delivery_items_1.*,
		mla_last_article_do.article_do_last_change,
		mla_last_sparepart_do.sparepart_do_last_change
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

	left JOIN
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
	AS mla_last_article_do

	ON mla_last_article_do.article_last_do_key = mla_delivery_items_1.article_do_key

	 left JOIN
	(
	/*Last Spartpart DO KEY*/

	select

		mla_delivery_items_1.sparepart_id,
		max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
		concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as sparepart_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as sparepart_do_date,
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

	ON mla_last_sparepart_do.sparepart_last_do_key = mla_delivery_items_1.spartpart_do_key
	/* DELIVERY ITEMS TABLE WITH LAST DO*/
)

AS mla_delivery_items_2

WHERE mla_delivery_items_2.article_do_last_change  IS NOT NULL OR mla_delivery_items_2.sparepart_do_last_change IS NOT NULL

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

) 

AS mla_delivery_items_3

ON mla_purchase_request_items_1.article_id = mla_delivery_items_3.article_id


/* LAST SP DO */
left JOIN
(
/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

SELECT 
	mla_delivery_items_2.*
FROM
(

	/* DELIVERY ITEMS TABLE WITH LAST DO*/
	select
		mla_delivery_items_1.*,
		mla_last_article_do.article_do_last_change,
		mla_last_sparepart_do.sparepart_do_last_change
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

	left JOIN
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
	AS mla_last_article_do

	ON mla_last_article_do.article_last_do_key = mla_delivery_items_1.article_do_key

	 left JOIN
	(
	/*Last Spartpart DO KEY*/

	select

		mla_delivery_items_1.sparepart_id,
		max(mla_delivery_items_1.created_on) as sparepart_do_last_change,
		concat(mla_delivery_items_1.sparepart_id,'+++',max(mla_delivery_items_1.created_on)) as sparepart_last_do_key

	from 
	(
		/*Delivery Items Table*/
		select 
			mla_delivery_items.*,
			concat(mla_purchase_request_items.article_id,'+++',mla_delivery_items.created_on) as article_do_date,
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.sparepart_id,'+++',mla_delivery_items.created_on) as sparepart_do_date,
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

	ON mla_last_sparepart_do.sparepart_last_do_key = mla_delivery_items_1.spartpart_do_key
	/* DELIVERY ITEMS TABLE WITH LAST DO*/
)

AS mla_delivery_items_2

WHERE mla_delivery_items_2.article_do_last_change  IS NOT NULL OR mla_delivery_items_2.sparepart_do_last_change IS NOT NULL

/* DELIVERY ITEMS TABLE WITH LAST DO FOR ARTICLE AND SPARE PART*/

) 

AS mla_delivery_items_4

ON mla_purchase_request_items_1.sparepart_id = mla_delivery_items_4.sparepart_id
	
WHERE " . $t_balance . " ORDER BY mla_purchase_request_items_1.EDT ASC
" . $t_limit . $t_offset;
	
		// echo($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 * =====================
	 */
	public function getMySubmittedPRItems($user_id) {
		$adapter = $this->tableGateway->adapter;
		$sql = "
SELECT TT3.pr_number, TT3.description, TT3.requested_on, TT3.tItems,TT3.status,TT3.last_change,TT3.requester , TT3.user_id, TT1.*, TT2.delivered_quantity from mla_purchase_request_items as TT1

LEFT JOIN
	
	(select t2.*, t3.created_on as delivered_on, sum(t1.delivered_quantity) as delivered_quantity, t3.created_by as delivered_by
	from mla_delivery_items as T1
	left join mla_purchase_request_items as T2
	On T2.id = T1.pr_item_id
	left join mla_delivery as T3
	on T1.delivery_id = t3.id
	group by T1.pr_item_id) as TT2

On TT2.id = TT1.id
	
JOIN
	
	(SELECT TB1.*, TB2.*, TB3.*, concat(TB4.firstname, ' ', TB4.lastname) as requester, TB4.id as user_id FROM mla_purchase_requests as TB1
	JOIN
		(select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1
		join mla_purchase_requests as t2
		on t1.purchase_request_id = t2.id    
		group by t2.id    
		) as TB2        
    ON TB2.pr_id =  TB1.id
   
	/* important, change to JOIN to show only summbited */
	JOIN				
		(select lt1.status,lt1.purchase_request_id, lt2.last_change from mla_purchase_requests_workflows as lt1
		Join 
		(select tt1.purchase_request_id,max(tt1.updated_on) as last_change from mla_purchase_requests_workflows as tt1
		Group by tt1.purchase_request_id) as lt2
		ON lt1.updated_on = lt2.last_change) as TB3

	ON TB1.id =  TB3.purchase_request_id

	
	LEFT join mla_users as TB4
	on TB4.id = TB1.requested_by
    
    /*CHANGE*/
    Where TB1.requested_by = " . $user_id . "
    ) as TT3
  	
on TT1.purchase_request_id = TT3.id
ORDER BY TT1.EDT ASC
		";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * ======================
	 *
	 * @param unknown $item        	
	 */
	public function getDeliveredOfItem($item) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "			
select sum(t1.delivered_quantity) as delivered_quantity
from mla_delivery_items as T1
left join mla_purchase_request_items as T2 
On T2.id = T1.pr_item_id
left join mla_delivery as T3
on T1.delivery_id = t3.id
WHERE T1.pr_item_id = " . $item . " group by T1.pr_item_id";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		$r = $resultSet->current ();
		if ($r) {
			return ( int ) $r ["delivered_quantity"];
		}
		return 0;
	}
	
	/**
	 * ==============================================
	 */
	public function getLastStatusPRItems() {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "
/* Last Workflow changed PR ITEM) */
select
	mla_purchase_requests_items_workflows_1.*
from 

	(select 
	mla_purchase_requests_items_workflows.*,
	concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',mla_purchase_requests_items_workflows.updated_on) as pr_item_id_changed_on
	from mla_purchase_requests_items_workflows) as mla_purchase_requests_items_workflows_1

join

	(select 
		max(mla_purchase_requests_items_workflows.updated_on) AS pr_item_last_change,
		concat(mla_purchase_requests_items_workflows.pr_item_id,'+++',max(mla_purchase_requests_items_workflows.updated_on)) as pr_item_id_lastchange_on,
		mla_purchase_requests_items_workflows.pr_item_id
		 from mla_purchase_requests_items_workflows
	group by mla_purchase_requests_items_workflows.pr_item_id) as mla_purchase_requests_items_workflows_2

on mla_purchase_requests_items_workflows_2.pr_item_id_lastchange_on = mla_purchase_requests_items_workflows_1.pr_item_id_changed_on
"
;
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
			
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;	
	}
	
	/**
	 * ======================
	 *
	 * @param unknown $item
	 */
	public function getOrderHistory($sparepart_id,$article_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
select
	mla_purchase_request_items.*,
	mla_purchase_requests.id as pr_id,
	mla_purchase_requests.pr_number
from mla_purchase_request_items
left join mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
where 1
		";
		
		if($sparepart_id>0){
			$sql= $sql .' AND  mla_purchase_request_items.sparepart_id = '.$sparepart_id;
		}
		
		if($article_id>0){
			$sql= $sql .' AND mla_purchase_request_items.article_id = '.$article_id;
		}
		
		$sql= $sql. ' ORDER BY mla_purchase_request_items.created_on DESC';
		
		$sql= $sql.";";
		
		//echo $sql;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if (count($resultSet)>0) {
			return $resultSet;
		}else {
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown $sp_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPendingPRItemsOfSparepart($sp_id){
	
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPRItemsWithDN_SQL_V2;
		
		$sql=$sql." AND (CASE WHEN (mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0))>=0
				THEN mla_purchase_request_items.quantity - IFNULL(mla_delivery_items_workflows.confirmed_quantity,0)  ELSE 0 END) >0";
		
		$sql = $sql . " AND mla_purchase_request_items.sparepart_id= ".$sp_id;
	
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	
	}
}