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
				'created_by' => $input->created_by,
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
				'remarks' => $input->remark,
				'created_by' => $input->created_by,
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
	
	public function getRoles($limit,$offset)
	{
		$sql =
"
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
		
" ;
		$sql = $sql. " order by mla_acl_roles.role";
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	public function getRole($id)
	{
		$sql =
		"
select
*
from mla_acl_roles
where 1
AND mla_acl_roles.id = " . $id ;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet->current();
	}
}