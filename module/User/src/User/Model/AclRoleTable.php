<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use User\Model\AclRole;

class AclRoleTable {
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
	public function add(AclRole $input) {
		$data = array (
				'role' => $input->role,
				'parent_id' => $input->parent_id,
				'path' => $input->path,
				'path_depth' => $input->path_depth,
				'status' => $input->status,
				'remarks' => $input->remarks,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param AssetCategory $input        	
	 * @param unknown $id        	
	 */
	public function update(AclRole $input, $id) {
		$data = array (
				'role' => $input->role,
				'parent_id' => $input->parent_id,
				'path' => $input->path,
				'path_depth' => $input->path_depth,
				'status' => $input->status,
				'remarks' => $input->remarks,
		);
		
		$where = 'id = ' . $id;
		return $this->tableGateway->update ( $data, $where );
	}
	
	/**
	 *
	 * @param AssetCategory $input
	 * @param unknown $id
	 */
	public function updateByRole(AclRole $input, $role) {
		$data = array (
				'role' => $input->role,
		);
	
		$where = "role = '" . $role ."'";
		return $this->tableGateway->update ( $data, $where );
	}
	
	/*
	 *
	 *
	 */
	public function delete($id) {
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getRoles($limit, $offset) {
		$sql = "
select
	mla_acl_roles.*,
	ifnull(mla_acl_user_role.total_members,0) as total_members
from mla_acl_roles
left join
(
	select
		mla_acl_user_role.role_id,	
		count(mla_acl_user_role.user_id) as total_members
	from  mla_acl_user_role
	group by mla_acl_user_role.role_id
) as mla_acl_user_role
on mla_acl_user_role.role_id = mla_acl_roles.id

where 1
		
";
		$sql = $sql . " order by mla_acl_roles.role";
		
		if ($limit > 0) {
			$sql = $sql . " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql . " OFFSET " . $offset;
		}
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	
	public function getRoleIDByName($role) {
		$sql = "
select
*
from mla_acl_roles
where 1
And mla_acl_roles.role='".$role ."'";
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
	
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if($resultSet->count()>0){
			return $resultSet->current()->id;
		}else {
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @return ArrayObject|NULL
	 */	
	public function getRole($id) {
		$sql = "
select
*
from mla_acl_roles
where 1
AND mla_acl_roles.id = " . $id;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet->current();
	}
	
	public function isRoleExits($role)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'role=?'=> $role,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_acl_roles'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
	public function isExits($id)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'id=?'		=> $id,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_acl_roles'));
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