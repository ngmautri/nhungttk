<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\DeliveryItem;	


class DeliveryItemTable {
	
	protected $tableGateway;
	
	protected $getDNItems_SQL = 
			"
select 
	mla_delivery_items.id as dn_item_id,
    mla_delivery_items.delivery_id,
    mla_delivery_items.pr_item_id,
    mla_delivery_items.name as dn_item_name,
    mla_delivery_items.code as dn_item_code,
	mla_delivery_items.unit as dn_item_unit,
    mla_delivery_items.delivered_quantity,
	mla_delivery_items.price,
 	mla_delivery_items.currency,
  	mla_delivery_items.vendor_id,
	mla_delivery_items.created_by as delivered_by,

	mla_delivery_items_workflows.status as dn_item_last_status,
	mla_delivery_items_workflows.updated_on as dn_item_last_status_on,
	mla_delivery_items_workflows.updated_by as dn_item_last_status_by,
    mla_purchase_request_items.*
from mla_delivery_items

left join mla_delivery_items_workflows
on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id

left join 
(
	select 
		mla_purchase_requests.pr_number as pr_number,
        mla_purchase_requests.auto_pr_number,
  		mla_purchase_requests.name as pr_name,
        mla_purchase_requests.requested_by as pr_requester_id,
		mla_purchase_request_items.*
    from mla_purchase_request_items
    left join mla_purchase_requests
    on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
)
as mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items.pr_item_id
where 1
			
			";
	
	private $getDOItem_SQL="

select
	
	mla_delivery_items.*,
    ifnull(mla_delivery_items_workflows.confirmed_quantity,0) as confirmed_quantity,
    ifnull(mla_delivery_items_workflows.rejected_quantity,0) as rejected_quantity,
	ifnull(mla_delivery_items_notified.notified_quantity,0) as notified_quantity,
    
     if ((mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))
    ,0) as confirmed_balance,
    
    mla_delivery_items_notified.dn_last_status,
    mla_vendors.name as vendor_name,
	
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name as pr_item_name,
    mla_purchase_request_items.code as pr_item_code,
     mla_purchase_request_items.unit as pr_item_unit,
    mla_purchase_request_items.keywords as pr_item_keywords,
	mla_purchase_request_items.ordered_quantity,
    mla_purchase_request_items.EDT,
    mla_purchase_request_items.sparepart_id,
    mla_purchase_request_items.article_id,
    mla_purchase_request_items.asset_id,


	mla_purchase_request_items.pr_id,
	mla_purchase_request_items.seq_number_of_year,
	mla_purchase_request_items.pr_number,
	mla_purchase_request_items.auto_pr_number,    
    mla_purchase_request_items.pr_name,
	mla_purchase_request_items.pr_description,
    mla_purchase_request_items.pr_requested_by,
    mla_purchase_request_items.pr_requested_on,
    mla_purchase_request_items.pr_last_status,
	mla_purchase_request_items.pr_last_status_on,
	mla_purchase_request_items.pr_last_status_by,
 	mla_purchase_request_items.pr_year,
 	mla_purchase_request_items.pr_requester_name,
	
    mla_purchase_request_items.pr_of_department_id,
	mla_purchase_request_items.pr_of_department,
 	mla_purchase_request_items.pr_of_department_status
 
   	from mla_delivery_items
    
/* total confirmed and rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity,
    sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_workflows
on mla_delivery_items_workflows.pr_item_id = mla_delivery_items.pr_item_id

/* total notified /unconfirmed DN */
left join
(
		select
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by,
         	mla_delivery_items.delivered_quantity  as notified_quantity
	
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
	    where mla_delivery_items_workflows.status = 'Notified'
)
as mla_delivery_items_notified
on mla_delivery_items_notified.dn_item_id = mla_delivery_items.id

     left join mla_vendors
	on mla_vendors.id = mla_delivery_items.vendor_id

/* Pr Items*/
left join
(

/* PR ITEMS*/
select
	mla_purchase_request_items.id,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name,
    mla_purchase_request_items.code,
     mla_purchase_request_items.unit,
    mla_purchase_request_items.keywords,
	mla_purchase_request_items.quantity as ordered_quantity,
	mla_purchase_request_items.EDT,
    mla_purchase_request_items.sparepart_id,
    mla_purchase_request_items.article_id,
    mla_purchase_request_items.asset_id,
    
    
	
    mla_purchase_requests.id as pr_id,
	mla_purchase_requests.seq_number_of_year,
	mla_purchase_requests.pr_number,
	mla_purchase_requests.auto_pr_number,    
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
 	mla_purchase_requests.pr_of_department_status
     
	        
from mla_purchase_request_items

/* purchase requests*/
left join
(
	select
		mla_purchase_requests.*,
		mla_purchase_requests_workflows.status as pr_last_status,
		mla_purchase_requests_workflows.updated_by as pr_last_status_by,
		mla_purchase_requests_workflows.updated_on as pr_last_status_on,
		year(mla_purchase_requests.requested_on) as pr_year,
		concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_status as pr_of_department_status
	from mla_purchase_requests

	left join mla_purchase_requests_workflows
		on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

	left join
	(
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

	) 
	as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by

) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* PR ITEMS*/

)
as  mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items.pr_item_id
    
    where 1
  		";
	
	private $getGRItem_SQL="
	select
	mla_delivery_items.*,
    ifnull(mla_delivery_items_workflows.confirmed_quantity,0) as confirmed_quantity,
    ifnull(mla_delivery_items_workflows.rejected_quantity,0) as rejected_quantity,
    
     if ((mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))
    ,0) as confirmed_balance,
	
	 if ((mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_workflows.confirmed_quantity,0)-mla_purchase_request_items.ordered_quantity) as confirmed_free_balance,
    
       
	ifnull(mla_delivery_items_notified.notified_quantity,0) as notified_quantity,
    mla_delivery_items_notified.dn_last_status,
    mla_vendors.name as vendor_name,
	
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name as pr_item_name,
    mla_purchase_request_items.code as pr_item_code,
     mla_purchase_request_items.unit as pr_item_unit,
    mla_purchase_request_items.keywords as pr_item_keywords,
	mla_purchase_request_items.ordered_quantity,

	mla_purchase_request_items.pr_id,

	mla_purchase_request_items.seq_number_of_year,
	mla_purchase_request_items.pr_number,
	mla_purchase_request_items.auto_pr_number,    
    mla_purchase_request_items.pr_name,
	mla_purchase_request_items.pr_description,
    mla_purchase_request_items.pr_requested_by,
    mla_purchase_request_items.pr_requested_on,
    mla_purchase_request_items.pr_last_status,
	mla_purchase_request_items.pr_last_status_on,
	mla_purchase_request_items.pr_last_status_by,
 	mla_purchase_request_items.pr_year,
 	mla_purchase_request_items.pr_requester_name,
	
    mla_purchase_request_items.pr_of_department_id,
	mla_purchase_request_items.pr_of_department,
 	mla_purchase_request_items.pr_of_department_status
 
   	from mla_delivery_items
    
  /* total confirmed and rejected DN */
left join
(
	select
	mla_delivery_items_workflows.pr_item_id,
	sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity,
    sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
	from mla_delivery_items_workflows
	group by mla_delivery_items_workflows.pr_item_id
)
as mla_delivery_items_workflows
on mla_delivery_items_workflows.pr_item_id = mla_delivery_items.pr_item_id

/* total notified /unconfirmed DN */
left join
(
		select
			mla_delivery_items_workflows.dn_item_id,
			mla_delivery_items_workflows.status as dn_last_status,
			mla_delivery_items_workflows.updated_on as dn_last_status_on,
			mla_delivery_items_workflows.updated_by as dn_last_status_by,
         	mla_delivery_items.delivered_quantity  as notified_quantity
	
		from mla_delivery_items
		left join mla_delivery_items_workflows
		on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
	    where mla_delivery_items_workflows.status = 'Notified'
)
as mla_delivery_items_notified
on mla_delivery_items_notified.dn_item_id = mla_delivery_items.id

     left join mla_vendors
	on mla_vendors.id = mla_delivery_items.vendor_id

/* Pr Items*/
left join
(

/* PR ITEMS*/
select
	mla_purchase_request_items.id,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name,
    mla_purchase_request_items.code,
     mla_purchase_request_items.unit,
    mla_purchase_request_items.keywords,
	mla_purchase_request_items.quantity as ordered_quantity,

	mla_purchase_requests.id as pr_id,

	mla_purchase_requests.seq_number_of_year,
	mla_purchase_requests.pr_number,
	mla_purchase_requests.auto_pr_number,    
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
 	mla_purchase_requests.pr_of_department_status
     
	        
from mla_purchase_request_items

/* purchase requests*/
left join
(
	select
		mla_purchase_requests.*,
		mla_purchase_requests_workflows.status as pr_last_status,
		mla_purchase_requests_workflows.updated_by as pr_last_status_by,
		mla_purchase_requests_workflows.updated_on as pr_last_status_on,
		year(mla_purchase_requests.requested_on) as pr_year,
		concat (mla_users.firstname,' ',mla_users.lastname ) as pr_requester_name,
		mla_users.department_id as pr_of_department_id,
		mla_users.department_name as pr_of_department,
		mla_users.department_status as pr_of_department_status
	from mla_purchase_requests

	left join mla_purchase_requests_workflows
		on mla_purchase_requests_workflows.id = mla_purchase_requests.last_workflow_id

	left join
	(
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

	) 
	as mla_users
	on mla_users.user_id = mla_purchase_requests.requested_by

) 
as mla_purchase_requests
on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

/* PR ITEMS*/

)
as  mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items.pr_item_id
    
    where 1
    and mla_delivery_items.po_item_id>0	
	";
	
	
	private $getGRItem_SQL_V1 =
	"
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
    
     
    mla_delivery_items.id AS dn_item_id,
    mla_delivery_items.vendor_id AS dn_vendor_id,
    mla_delivery_items.price AS dn_price,
    mla_delivery_items.currency AS dn_currency,
    mla_delivery_items.payment_method AS dn_payment_method,
    mla_vendors_dn.name as  dn_vendor_name,
    
	mla_delivery_items.delivery_id,
    mla_delivery_items.delivered_quantity,
    mla_delivery_items.last_workflow_id as dn_item_last_workflow_id,
    
    ifnull(CASE WHEN mla_delivery_items_workflows_received.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END,0) AS dn_item_unconfirmed_quantity,
    
    
    mla_delivery_items_workflows_received.confirmed_quantity AS dn_item_confirmed_quantity,
    mla_delivery_items_workflows_received.rejected_quantity AS dn_item_rejected_quantity,
    mla_delivery_items_workflows_received.status AS dn_item_status
    
	
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

LEFT JOIN mla_delivery_items
ON mla_delivery_items.po_item_id = mla_po_item.id

LEFT JOIN mla_delivery_items_workflows AS mla_delivery_items_workflows_received
ON mla_delivery_items_workflows_received.id = mla_delivery_items.last_workflow_id

LEFT JOIN mla_vendors AS mla_vendors_dn
ON mla_vendors_dn.id = mla_delivery_items.vendor_id

/* PO ITEMS 1-1*/

WHERE 1
AND mla_purchase_requests_workflows.status IS NOT NULL
AND mla_delivery_items.po_item_id>0	

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
	 * @param unknown $id
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
	public function get($id){
		
		$id  = (int) $id;
		
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	
	/**
	 *
	 * @param unknown $dn_item_id
	 * @param unknown $last_workflow_id
	 */
	public function getDOItem($do_item_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql ="
		select 
			mla_delivery_items.*,
			mla_vendors.name as vendor_name
		from mla_delivery_items
		join mla_vendors
		on mla_vendors.id = mla_delivery_items.vendor_id where mla_delivery_items.id = " . $do_item_id;
	
		//echo $sql;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if(count($resultSet)>0){
			return $resultSet->current();
		}else{
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown $dn_item_id
	 * @param unknown $last_workflow_id
	 */
	public function updateLastWorkFlow($dn_item_id,$last_workflow_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql =
		"update mla_delivery_items
		set last_workflow_id = " . $last_workflow_id.
			" where id = " . $dn_item_id;
	
		//echo $sql;
	
		$statement = $adapter->query ( $sql );
		$statement->execute ();
	}
	
	/*
	 * 
	public $id;
	public $receipt_date;
	public $delivery_date;

	public $delivery_id;
	public $po_item_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	public $unit;

	public $delivered_quantity;
	public $price;
	public $currency;
	public $payment_method;
	public $vendor_id;

	public $remarks;
	
	public $created_by;
	public $created_on;
	
	public $last_workflow_id;
	
	public $invoice_no;
	public $invoice_date;
	 */
	public function add(DeliveryItem $input) {
		$data = array (
				'receipt_date' => $input->receipt_date,
				'delivery_date' => $input->delivery_date,
				
				'delivery_id' => $input->delivery_id,
				'po_item_id' => $input->po_item_id,
				'pr_item_id' => $input->pr_item_id,
				
				
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				
				'delivered_quantity' => $input->delivered_quantity,
				'price' => $input->price,
				'currency' => $input->currency,
				'payment_method' => $input->payment_method,
				'vendor_id' => $input->vendor_id,
				'remarks' => $input->remarks,
				
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				
				'invoice_no' => $input->invoice_no,
				'invoice_date' => $input->invoice_date,
				
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param DeliveryItem $input
	 * @param unknown $id
	 */
	public function update(DeliveryItem $input, $id) {
		
		$data = array (
				
				'receipt_date' => $input->receipt_date,
				'delivery_date' => $input->delivery_date,
				
				'delivery_id' => $input->delivery_id,
				'po_item_id' => $input->po_item_id,
				'pr_item_id' => $input->pr_item_id,
				
				
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				
				'delivered_quantity' => $input->delivered_quantity,
				'price' => $input->price,
				'currency' => $input->currency,
				'payment_method' => $input->payment_method,
				'vendor_id' => $input->vendor_id,
				'remarks' => $input->remarks,
				
				'created_by' => $input->created_by,
				
				'invoice_no' => $input->invoice_no,
				'invoice_date' => $input->invoice_date,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	/**
	 *
	 * @param DeliveryItem $input
	 * @param unknown $id
	 */
	public function updateGR(DeliveryItem $input, $id) {
	
		$data = array (
				'receipt_date' => $input->receipt_date,
				'delivery_date' => $input->delivery_date,
	
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
	
				'delivered_quantity' => $input->delivered_quantity,
				'price' => $input->price,
				'currency' => $input->currency,
				'payment_method' => $input->payment_method,
				'vendor_id' => $input->vendor_id,
				'remarks' => $input->remarks,
	
				'invoice_no' => $input->invoice_no,
				'invoice_date' => $input->invoice_date,
		);
	
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	/**
	 *
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getReceivedItemList(){
		$adapter = $this->tableGateway->adapter;
	
		$sql ="";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	/**
	 * 
	 * @param unknown $seletect_items. $seletect_items must look like <code> (12,13,14)</code)
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function setReceivedItemsAsNotified($selected_items) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update
(
   mla_delivery_items
)
set  mla_delivery_items.status  = 'NOTIFIED'
where 1
AND mla_delivery_items.id  IN " . $selected_items;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	

	public function getItemsByDN($dn) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "
	SELECT 
		mla_delivery_items.*,
		mla_purchase_request_items.purchase_request_id,
		mla_purchase_request_items.pr_number,
        mla_purchase_request_items.auto_pr_number,
		mla_purchase_request_items.priority,
		
		mla_purchase_request_items.quantity,
		mla_purchase_request_items.EDT,
		mla_purchase_request_items.article_id,
		mla_purchase_request_items.sparepart_id,
		mla_purchase_request_items.asset_id,
		
		mla_purchase_request_items.created_on as requested_on,
		mla_purchase_request_items.created_by as requested_by,
		mla_purchase_request_items.firstname,
		mla_purchase_request_items.lastname,
		mla_purchase_request_items.email,
        mla_delivery_items_workflows.status as do_item_status
		
	FROM mla_delivery_items
	JOIN
	(
		select
			mla_purchase_requests.pr_number,
            mla_purchase_requests.auto_pr_number,
			mla_purchase_request_items.*,
			mla_users.firstname,
			mla_users.lastname,
			mla_users.email
		from mla_purchase_request_items
		join mla_users
		on mla_users.id = mla_purchase_request_items.created_by
        
        join mla_purchase_requests
        on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
        
	) as mla_purchase_request_items
	ON mla_purchase_request_items.id = mla_delivery_items.pr_item_id

	left join mla_delivery_items_workflows
    on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id


WHERE 1
		";
		$sql = $sql . " AND mla_delivery_items.delivery_id =". $dn;	
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNotifiedDNItemsOf($user_id, $pr_item_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getDOItem_SQL;
		$sql = $sql. 
		" AND mla_delivery_items_notified.dn_last_status  = 'notified'
		  AND mla_purchase_request_items.pr_requested_by =" . $user_id;
		if($pr_item_id>0){
			$sql = $sql.
			" AND mla_delivery_items.pr_item_id = ".$pr_item_id;
		}
		
		$sql = $sql.";";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function setDNItemsAsNotified($dn_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update
(
   mla_delivery_items
)
set  mla_delivery_items.status  = 'NOTIFIED'
where 1
and mla_delivery_cart.id  IN " . $seletect_items;
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getDOItemsByPOItem($po_item_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getDOItem_SQL;
		$sql = $sql . " AND mla_delivery_items.po_item_id=".$po_item_id;

	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getDOItems($limit,$offset) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getDOItem_SQL;
		
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getGRItems($balance,$notified,$notified_quantity,$limit,$offset) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getGRItem_SQL;
		
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.ordered_quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
		
		if ($notified ==1) {
			$sql = $sql. " AND mla_delivery_items.last_workflow_id >0";
		}
		if ($notified ==0) {
			$sql = $sql. " AND mla_delivery_items.last_workflow_id is null";;
		}
		
		// unconfirmed
		if ($notified_quantity == 0) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_notified.notified_quantity,0)) = 0";
		}
		if ($notified_quantity ==1) {
			$sql = $sql. " AND (ifnull(mla_delivery_items_notified.notified_quantity,0)) > 0";
		}
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $user_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getGRItems_V1($balance,$notified,$notified_quantity,$limit,$offset) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getGRItem_SQL_V1;
	
		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.quantity - ifnull(mla_delivery_items_workflows.confirmed_quantity,0)) > 0";
		}
	
		if ($notified ==1) {
			$sql = $sql. " AND mla_delivery_items.last_workflow_id >0";
		}
		if ($notified ==0) {
			$sql = $sql. " AND mla_delivery_items.last_workflow_id is null";;
		}
		
		// unconfirmed
		if ($notified_quantity == 0) {
			$sql = $sql. " AND ifnull(CASE WHEN mla_delivery_items_workflows_received.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END,0)= 0";
		}
		if ($notified_quantity ==1) {
			$sql = $sql. " AND ifnull(CASE WHEN mla_delivery_items_workflows_received.status='Notified' THEN  mla_delivery_items.delivered_quantity ELSE 0 END,0) > 0";
		}
		
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		$sql = $sql.";";
		//echo $sql;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $seletect_items
	 * @param unknown $dn_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function submitSelectedDOItems($seletect_items, $dn_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update  mla_delivery_items
set mla_delivery_items.delivery_id = ". $dn_id .
" Where 1 AND mla_delivery_items.id IN ". $seletect_items;

		echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}

}