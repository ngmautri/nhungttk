<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use User\Model\AclRoleResource;

class AclRoleResourceTable {
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
	public function add(AclRoleResource $input) {
		$data = array (
				'role_id' => $input->role_id,
				'resource_id' => $input->resource_id,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param AssetCategory $input        	
	 * @param unknown $id        	
	 */
	public function update(AclRoleResource $input, $id) {
		$data = array (
				'role_id' => $input->role_id,
				'resource_id' => $input->resource_id,
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
	
	public function isGrantedAccess($role_id, $resource_id)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'role_id=?'		=> $role_id,
				'resource_id=?' 	=> $resource_id
		);
	
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_acl_role_resource'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
}