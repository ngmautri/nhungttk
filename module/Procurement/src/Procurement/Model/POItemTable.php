<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\POItem;

class POItemTable {
	
	protected $tableGateway;
	
	private $getPOItems_SQL ="
		select
            mla_po_item.*,
            mla_vendors.name as vendor_name,
            mla_purchase_request_items.*,
            ifnull( mla_delivery_items.total_received_quantity,0) as total_received_quantity
    		from mla_po_item
    
			left join
            (
            select
				mla_delivery_items.po_item_id,
				mla_delivery_items.pr_item_id,
				sum(mla_delivery_items.delivered_quantity) as total_received_quantity
				from mla_delivery_items
				group by mla_delivery_items.po_item_id
			) 
			as mla_delivery_items
            on mla_delivery_items.po_item_id = mla_po_item.id
            
            left join mla_vendors
            on mla_vendors.id = mla_po_item.vendor_id
 
			left join 
            (
/* ALL PR ITEMS*/
select
	mla_purchase_request_items.id as pr_item_id_1,
    mla_purchase_request_items.priority,
    mla_purchase_request_items.name as pr_item_name,
    mla_purchase_request_items.code as pr_item_code,
     mla_purchase_request_items.unit as pr_item_unit,
    mla_purchase_request_items.keywords as pr_item_keywords,
	mla_purchase_request_items.quantity as ordered_quantity,
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
 	mla_purchase_requests.pr_of_department_status,
     
	ifnull(mla_delivery_items_confirmed.confirmed_quantity,0) as confirmed_quantity,
	ifnull(mla_delivery_items_rejected.rejected_quantity,0) as rejected_quantity,
    
    if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
    ,(mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))
    ,0) as confirmed_balance,
    
	 if ((mla_purchase_request_items.quantity - ifnull(mla_delivery_items_confirmed.confirmed_quantity,0))>=0
	, 0
	,ifnull(mla_delivery_items_confirmed.confirmed_quantity,0)-mla_purchase_request_items.quantity) as confirmed_free_balance,
     ifnull(mla_delivery_items_notified.unconfimed_quantity,0) as unconfirmed_quantity
        
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


)
           as mla_purchase_request_items
			on mla_purchase_request_items.pr_item_id_1 = mla_po_item.pr_item_id
            where 1
	";
	
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
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
	public $id;
	public $po_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	public $unit;
	
	public $price;
	public $currency;
	public $payment_method;
	public $vendor_id;
	
	public $remarks;
	
	public $created_by;
	public $created_on;
	
	public $status;
	 *
	 * @param DeliveryCart $input        	
	 * @return number
	 */
	public function add(POItem $input) {
		$data = array (
				'po_id' => $input->po_id,
				'pr_item_id' => $input->pr_item_id,
				
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				
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
	 * No need to update pr_item_id
	 * 
	 * @param POItem $input
	 * @param unknown $id
	 */
	public function update(POItem $input, $id) {
		$data = array (
				'po_id' => $input->po_id,
				
				'name' => $input->name,
				'code' => $input->code,
				'unit' => $input->unit,
				
				'price' => $input->price,
				'currency' => $input->currency,
				'payment_method' => $input->payment_method,
				'vendor_id' => $input->vendor_id,
				'remarks' => $input->remarks,
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
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	
	/**
	 * 
	 * @param unknown $balance
	 * @param unknown $department_id
	 * @param unknown $vendor_id
	 * @param unknown $payment_methode
	 * @param unknown $currency
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet|NULL
	 */
	public function getPOItems($balance,$department_id,$vendor_id, $payment_methode, $currency, $limit, $offset) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getPOItems_SQL;
		
		if($vendor_id >0){
				$sql=$sql." AND mla_vendors.id=". $vendor_id;
		}
		
		if($department_id >0){
			$sql=$sql." AND mla_purchase_request_items.pr_of_department_id=". $department_id;
		}
		

		if ($balance == 0) {
			$sql = $sql. " AND (mla_purchase_request_items.ordered_quantity - ifnull(mla_purchase_request_items.confirmed_quantity,0)) = 0";
		}
		if ($balance ==1) {
			$sql = $sql. " AND (mla_purchase_request_items.ordered_quantity - ifnull(mla_purchase_request_items.confirmed_quantity,0)) > 0";
		}
		if ($balance ==-1) {
			$sql = $sql. " AND (mla_purchase_request_items.ordered_quantity - ifnull(mla_purchase_request_items.confirmed_quantity,0)) < 0";
		}
		

		if ($payment_methode != null or $payment_methode != '') {
			$sql = $sql. " AND mla_po_item.payment_method='".$payment_methode."'";
		}
		
		if ($currency != null or $currency != '') {
			$sql = $sql. " AND mla_po_item.currency='".$currency."'";
		}
		
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
	 *
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet|NULL
	 */
	public function getVendorsOfPOList() {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
	
			select
				mla_po_item.vendor_id,
				mla_vendors.name as vendor_name,
                count(mla_po_item.pr_item_id) as items_by_vendor
          	from mla_po_item
	
			left join mla_vendors
            on mla_vendors.id = mla_po_item.vendor_id
            where mla_po_item.status='SAVED'
            group by mla_po_item.vendor_id
            order by mla_vendors.name
		";
	
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
	public function getPOItem($id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = "
			select
				mla_po_item.*,
				mla_vendors.name as vendor_name
			from mla_po_item
			left join mla_vendors
			on mla_vendors.id=mla_po_item.vendor_id
			Where mla_po_item.id = 
		". $id;
		
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		if ($resultSet->count () > 0) {
			return $resultSet->current();
		} else {
			return null;
		}
	}
	
}