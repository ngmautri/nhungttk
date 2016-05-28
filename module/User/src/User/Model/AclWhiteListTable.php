<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use User\Model\AclWhiteList;

class AclWhiteListTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * @param User $input        	
	 */
	public function add(AclWhiteList $input) {
		$data = array (
				'resource_id' => $input->resource_id,
				'status' => $input->status,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param AssetCategory $input        	
	 * @param unknown $id        	
	 */
	public function update(AclWhiteList $input, $id) {
		$data = array (
			'resource_id' => $input->resource_id,
			'status' => $input->status,
		);
		
		$where = 'id = ' . $id;
		return $this->tableGateway->update ( $data, $where );
	}
	
	/*
	 *
	 *
	 */
	public function delete($id) {
	}
}