<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\DeliveryItem;	


class DeliveryItemTable {
	
	protected $tableGateway;
	
	protected $getDNItems_SQL = 
			"
select
*
from 
(
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
  		mla_purchase_requests.name as pr_name,
        mla_purchase_requests.requested_by as pr_requester_id,
		mla_purchase_request_items.*
    from mla_purchase_request_items
    left join mla_purchase_requests
    on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id
)
as mla_purchase_request_items
on mla_purchase_request_items.id = mla_delivery_items.pr_item_id
)
as mla_purchase_request_items
			";
	
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	
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
	
		/*
			$sql = "SELECT *
			FROM mla_purchase_request_items
			WHERE purchase_request_id = ". $pr .
			" ORDER BY EDT ASC";
			*/
	
	
		$sql = "
SELECT
*
From
(
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
		mla_purchase_request_items.email
		
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
)
AS mla_delivery_items
WHERE 1";
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
	public function getNotifiedDNItemsOf($user_id) {
		$adapter = $this->tableGateway->adapter;
	
		$sql = $this->getDNItems_SQL;
		$sql = $sql. 
		" WHERE mla_purchase_request_items.dn_item_last_status  = 'notified'
		  AND mla_purchase_request_items.pr_requester_id =" . $user_id;
		
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

}