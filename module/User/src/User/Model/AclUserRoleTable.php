<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use User\Model\AclUserRole;

class AclUserRoleTable {
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
	public function add(AclUserRole $input) {
		$data = array (
				'role_id' => $input->role_id,
				'user_id' => $input->user_id,
				'updated_by' => $input->updated_by,
				
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
				'role_id' => $input->role_id,
				'user_id' => $input->user_id,
				'updated_by' => $input->updated_by,
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
	
	/**
	 *
	 * @param unknown $user_id
	 * @param unknown $department_id
	 * @return boolean
	 */
	public function isMember($user_id, $role_id)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'user_id=?'		=> $user_id,
				'role_id=?' 	=> $role_id
		);
	
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_acl_user_role'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown $role_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNoneMembersOfRole($role_id)
	{
		$sql_MemberOfRole = 	
	"
	SELECT
		mla_users.id
	FROM mla_acl_user_role 
	LEFT JOIN mla_users
		on mla_users.id = mla_acl_user_role.user_id
	WHERE 1
	AND mla_acl_user_role.role_id = " . $role_id;	
		
		$sql = 
		"select
		* 
		from mla_users
		where mla_users.id Not in
		(".
			$sql_MemberOfRole
		.				
		")";
		
		
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 */
	public function getRoleByUserId($user_id)
	{
		$sql =
		"
		select
		 *
        from
        (
	        select 
				mla_acl_user_role. *,
				mla_acl_roles.role as role,
				mla_acl_roles.parent_id,
				mla_acl_roles.path
			from mla_acl_user_role
			join mla_acl_roles
			on mla_acl_roles.id = mla_acl_user_role.role_id
        ) 
        as mla_acl_user_role
        where 1 AND mla_acl_user_role.user_id = " . $user_id;
		
			$adapter = $this->tableGateway->adapter;
			$statement = $adapter->query($sql);
	
			$result = $statement->execute();
	
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($result);
			return $resultSet;
	}
	
}