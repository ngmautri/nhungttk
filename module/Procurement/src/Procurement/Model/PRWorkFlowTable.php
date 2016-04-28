<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PRWorkFlow;	


class PRWorkFlowTable {
	
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
	
	/**
	public $id;
	public $purchase_request_id;
	public $status;
	
	public $updated_on;
	public $updated_by;
	 * @param PurchaseRequest $input
	 */
	public function add(PRWorkFlow $input) {
		$data = array (
				'purchase_request_id' => $input->purchase_request_id,
				'status' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(PurchaseRequest $input, $id) {
		
		$data = array (
			'pr_number' => $input->purchase_request_id,
				'name' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
				
		);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
}