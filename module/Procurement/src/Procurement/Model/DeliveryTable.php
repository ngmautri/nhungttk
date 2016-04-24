<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\Delivery;	


class DeliveryTable {
	
	protected $tableGateway;
	
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
	
	
	public function add(Delivery $input) {
		$data = array (
				'dn_number' => $input->dn_number,
				'description' => $input->description,
				
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(PurchaseRequest $input, $id) {
		
		$data = array (
				'dn_number' => $input->dn_number,
				'description' => $input->description,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function getDeliveryOf($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		
		$sql = "SELECT *
		FROM mla_delivery as TB1		
		LEFT JOIN (select count(*) as tItems, t2.id as dn_id from mla_delivery_items as t1 
join mla_delivery as t2 
on t1.delivery_id = t2.id
group by t2.id) as TB2
		ON TB2.dn_id =  TB1.id
				
		WHERE TB1.created_by = ". $user_id;
		/*
		$sql = "SELECT *
		FROM mla_purchase_requests as t1
		LEFT JOIN mla_purchase_request_items as t2
		on t2.purchase_request_id = t1.id
		WHERE requested_by = ". $user_id;
		*/
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	public function getDelivery($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "select t1.*, concat(t2.firstname, ' ', t2.lastname) as requester from mla_purchase_requests as t1
		left join mla_users as t2
		on t2.id = t1.requested_by
		WHERE t1.id = ". $id;
		/*
			$sql = "SELECT *
			FROM mla_purchase_requests as t1
			LEFT JOIN mla_purchase_request_items as t2
			on t2.purchase_request_id = t1.id
			WHERE requested_by = ". $user_id;
			*/
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		$row = $resultSet->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;		
	}
}