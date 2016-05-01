<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PRItemWorkFlow;	


class PRItemWorkFlowTable {
	
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
	public $pr_item_id;
	public $status;
	
	public $updated_on;
	public $updated_by;
	 * @param PurchaseRequest $input
	 */
	public function add(PRItemWorkFlow $input) {
		$data = array (
				'pr_item_id' => $input->pr_item_id,
				'delivery_id' => $input->delivery_id,
				
				'status' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param PRItemWorkFlow $input
	 * @param unknown $id
	 */
	public function update(PRItemWorkFlow $input, $id) {
		
		$data = array (
				'pr_item_id' => $input->pr_item_id,
				'delivery_id' => $input->delivery_id,
				
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