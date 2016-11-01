<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Model\UOM;

class UOMTable {
	
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	public function get($id) {
		$id = ( int ) $id;
		
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}

	
	 /**
	  * 
		public $id;
		public $name;
		public $description;
		public $status;
		public $created_on;
		public $created_by;		
	  * @param Department $input
	  */
	public function add(UOM $input) {
		
		$data = array (
				'uom_name' => $input->uom_name,
				'uom_description' => $input->uom_description,
				'status' => $input->status,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/*
	 * 
	 */
	public function update(UOM $input, $id) {
		$data = array (
				'uom_name' => $input->uom_name,
				'uom_description' => $input->uom_description,
				'status' => $input->status,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	
}