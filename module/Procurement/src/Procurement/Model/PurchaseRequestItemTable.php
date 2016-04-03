<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

use Procurement\Model\PurchaseRequestItem;	


class PurchaseRequestItemTable {
	
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
	
	
	public function add(PurchaseRequestItem $input) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	
	public function update(PurchaseRequestItem $input, $id) {
		
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'priority' => $input->priority,
				'name' => $input->name,
				'description' => $input->description,
				'unit' => $input->unit,
				'quantity' => $input->quantity,
				'EDT' => $input->EDT,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function getItemsByPR($pr) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "SELECT *
		FROM mla_purchase_request_items 
		WHERE purchase_request_id = ". $pr .
		" ORDER BY EDT ASC";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
}