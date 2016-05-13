<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\DeliveryItemWorkFlow;	


/**
 * 
 * @author nmt
 *
 */
class DeliveryItemWorkFlowTable {
	
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
	public $delivery_id;
	public $dn_item_id;
	public $pr_item_id;
	public $status;
	public $updated_on;
	public $updated_by;
	
	 * @param PurchaseRequest $input
	 */
	public function add(DeliveryItemWorkFlow $input) {
		$data = array (
				'delivery_id' => $input->delivery_id,
				'dn_item_id' => $input->dn_item_id,
				'pr_item_id' => $input->pr_item_id,
				
				'status' => $input->status,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(DeliveryWorkFlow $input, $id) {
		
		$data = array (
				'delivery_id' => $input->delivery_id,
				'dn_item_id' => $input->dn_item_id,
				'pr_item_id' => $input->pr_item_id,
				
				'status' => $input->status,
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