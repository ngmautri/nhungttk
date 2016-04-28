<?php

namespace Procurement\Model;

use Zend\Db\TableGateway\TableGateway;
use Procurement\Model\Vendor;	


class VendorTable {
	
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
	public $name;
	public $keywords;
	public $status;
	public $created_on;
	public $created_by;
	 * 
	 *
	 * @param Delivery $input
	 */
	public function add(Vendor $input) {
		$data = array (
				'dn_number' => $input->name,
				'keywords' => $input->keywords,
				'status' => $input->status,
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(Vendor $input, $id) {
		
		$data = array (
				'dn_number' => $input->name,
				'keywords' => $input->keywords,
				'status' => $input->status,
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
}