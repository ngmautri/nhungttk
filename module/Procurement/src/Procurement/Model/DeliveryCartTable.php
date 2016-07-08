<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\DeliveryCart;

class DeliveryCartTable {
	
	private $getCartItems_SQL = "
			select
            mla_delivery_cart.*,
            mla_vendors.name as vendor_name,
            mla_purchase_request_items.quantity as ordered_quantity,
            mla_purchase_request_items.pr_requester_name
            
			from mla_delivery_cart
            
            left join mla_vendors
            on mla_vendors.id = mla_delivery_cart.vendor_id
 
			left join 
            (
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
            )
            as mla_purchase_request_items
            
			on mla_purchase_request_items.id = mla_delivery_cart.pr_item_id
 
 
            where 1
			and mla_delivery_cart.status ='SAVED'";
	
	private $getCatItems_SQL_V1 = 
	"
select *
from
(
			select
            mla_delivery_cart.*,
            mla_vendors.name as vendor_name,
            mla_purchase_request_items.*           
			from mla_delivery_cart
            
            left join mla_vendors
            on mla_vendors.id = mla_delivery_cart.vendor_id
 
			left join 
            (
/* ALL PR ITEMS*/

select
*
from
(
select
	mla_purchase_request_items.id as pr_item_id_1,
	mla_purchase_request_items.quantity as ordered_quantity,

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
			)
            as mla_purchase_request_items
			on mla_purchase_request_items.pr_item_id_1 = mla_delivery_cart.pr_item_id
            where 1
			and mla_delivery_cart.status ='SAVED'
)
as mla_delivery_cart
Where 1
			
	";
			
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 *
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
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
	 * @param DeliveryCart $input        	
	 * @return number
	 */
	public function add(DeliveryCart $input) {
		$data = array (
				'delivery_date' => $input->delivery_date,
				'delivery_id' => $input->delivery_id,
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
				'status' => $input->status 
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
				'delivery_date' => $input->delivery_date,
				'delivery_id' => $input->delivery_id,
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
				'status' => $input->status 
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	
	/**
	 *
	 * @param unknown $id        	
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @return number
	 */
	public function getTotalCartItems() {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "
			select
			count(*) as cart_items
			from mla_delivery_cart
			where 1
			and mla_delivery_cart.status ='SAVED'";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet->current ()->cart_items;
		} else {
			return 0;
		}
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @return number
	 */
	public function getCartItems($limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = $this->getCartItems_SQL;
		
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql . " OFFSET " . $offset;
		}
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet;
		} else {
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet|NULL
	 */
	public function getDNCartItems($limit, $offset) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getCatItems_SQL_V1;
		
		$sql = $sql. " ORDER BY pr_requested_by";
		
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql . " OFFSET " . $offset;
		}
		
		
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet;
		} else {
			return null;
		}
	}
	
	/**
	 * Add selected cart items as pr items.
	 *
	 * @param unknown $user_id
	 * @param unknown $pr_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function submitSelectedCartItems($seletect_items, $dn_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
insert into mla_delivery_items
(
	mla_delivery_items.delivery_date,
	mla_delivery_items.delivery_id,
    mla_delivery_items.pr_item_id,
    mla_delivery_items.name,
    mla_delivery_items.code,
	mla_delivery_items.unit,
    mla_delivery_items.delivered_quantity,
    mla_delivery_items.price,
    mla_delivery_items.currency,
    mla_delivery_items.payment_method,
    mla_delivery_items.vendor_id,
    mla_delivery_items.created_on,
    mla_delivery_items.created_by,
    mla_delivery_items.remarks
 )   
select
	mla_delivery_cart.delivery_date,
	".$dn_id.",
	mla_delivery_cart.pr_item_id,
    mla_delivery_cart.name,
    mla_delivery_cart.code,
	mla_delivery_cart.unit,
    mla_delivery_cart.delivered_quantity,
    mla_delivery_cart.price,
    mla_delivery_cart.currency,
    mla_delivery_cart.payment_method,
    mla_delivery_cart.vendor_id,
    mla_delivery_cart.created_on,
    mla_delivery_cart.created_by,
    mla_delivery_cart.remarks
from mla_delivery_cart
where 1
and mla_delivery_cart.id IN " . $seletect_items;
	
		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 *
	 * @param unknown $seletect_items must look like <code> (12,13,14)</code)
	 */
	public function setSelectedCartItemsAsNotified($seletect_items) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
update
(
   mla_delivery_cart
)
set  mla_delivery_cart.status  = 'NOTIFIED'
where 1
and mla_delivery_cart.id  IN " . $seletect_items;
	
		// echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
}