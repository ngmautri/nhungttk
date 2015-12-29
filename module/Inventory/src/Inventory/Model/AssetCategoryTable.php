<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetCategory;	


class AssetCategoryTable {
	
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
	
	
	public function add(AssetCategory $input) {
		$data = array (
				'category' => $input->category,
				'description' => $input->description,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		$resultSet = $this->tableGateway->insert ( $data );
	}
	
	public function update(AssetCategory $input, $id) {
		$data = array (
				'category' => $input->category,
				'description' => $input->description,
				'created_on' => date ( 'Y-m-d H:i:s' )
		);	
		
		$where = 'id = ' . $id;
		$resultSet = $this->tableGateway->update( $data,$where);
	}
	
	
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
}