<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\PRItemSelfConfirmation;	


class PRItemSelfConfirmationTable {
	
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
	 * 
	 * @param PRItemSelfConfirmation $input
	 * @return number
	 */
	public function add(PRItemSelfConfirmation $input) {
		$data = array (
				'pr_item_id' => $input->pr_item_id,
				'confirmed_quantity' => $input->confirmed_quantity,
				'rejected_quantity' => $input->rejected_quantity,
					
				'status' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
				'remarks' => $input->remarks,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param PRItemWorkFlow $input
	 * @param unknown $id
	 */
	public function update(PRItemSelfConfirmation $input, $id) {
		$data = array (
				'pr_item_id' => $input->pr_item_id,
				'confirmed_quantity' => $input->confirmed_quantity,
				'rejected_quantity' => $input->rejected_quantity,
					
				'status' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
				'remarks' => $input->remarks,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
}