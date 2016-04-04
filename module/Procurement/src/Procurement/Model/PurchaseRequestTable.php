<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PurchaseRequest;	


class PurchaseRequestTable {
	
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
	
	
	public function add(PurchaseRequest $input) {
		$data = array (
				'pr_number' => $input->pr_number,
				'name' => $input->name,
				'description' => $input->description,
				
				'requested_by' => $input->requested_by,
				'requested_on' => date ( 'Y-m-d H:i:s' ),

				'verified_by' => $input->verified_by,
				'verified_on' => $input->verified_on,
				
				'approved_by' => $input->approved_by,
				'approved_on' => $input->approved_on,
				
				'released_by' => $input->released_by,
				'released_on' => $input->released_on,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(PurchaseRequest $input, $id) {
		
		$data = array (
				'pr_number' => $input->pr_number,
				'name' => $input->name,
				'description' => $input->description,
				
				'requested_by' => $input->requested_by,
				'reqeusted_on' => date ( 'Y-m-d H:i:s' ),

				'verified_by' => $input->verified_by,
				'verified_on' => $input->verified_on,
				
				'approved_by' => $input->approved_by,
				'approved_on' => $input->approved_on,
				
				'released_by' => $input->released_by,
				'released_on' => $input->released_on,
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function getPRof($user_id) {
		$adapter = $this->tableGateway->adapter;
		
		
		$sql = "SELECT *
		FROM mla_purchase_requests as TB1		
		LEFT JOIN (select count(*) as tItems, t2.id as pr_id from mla_purchase_request_items as t1 
join mla_purchase_requests as t2 
on t1.purchase_request_id = t2.id
group by t2.id) as TB2
		ON TB2.pr_id =  TB1.id
				
		WHERE TB1.requested_by = ". $user_id;
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
	
	public function getPR($id) {
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