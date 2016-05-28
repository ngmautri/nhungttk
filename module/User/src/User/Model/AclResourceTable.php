<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use User\Model\AclResource;

class AclResourceTable {
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
	public function add(AclResource $input) {
		$data = array (
				'resource' => $input->resource,
				'type' => $input->type,
				'remarks' => $input->remarks,
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
	public function update(AclResource $input, $id) {
		$data = array (
				'resource' => $input->resource,
				'type' => $input->type,
				'remarks' => $input->remarks,
				'updated_on' => date ( 'Y-m-d H:i:s' ) 
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
	 * @param unknown $limit
	 * @param unknown $offset
	 */
	public function getResources($limit,$offset)
	{
	
$sql =
"select
mla_acl_resources.*,
if(isnull(mla_acl_whitelist.resource_id),0,1) as isWhiteList
from mla_acl_resources
left join mla_acl_whitelist
on mla_acl_whitelist.resource_id = mla_acl_resources.id
where 1
" ;
		$sql = $sql. " order by mla_acl_resources.resource";

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
	
	/**
	 *
	 * @param unknown $limit
	 * @param unknown $offset
	 */
	public function getAclRoleResources()
	{
	
		$sql =
"
select
	mla_acl_roles.*,
	mla_acl_resources.resource,
	mla_acl_resources.type
from
(
	select 
	mla_acl_roles.role,
    mla_acl_role_resource.resource_id
	from mla_acl_roles
	join mla_acl_role_resource
		on mla_acl_role_resource.role_id = mla_acl_roles.id
   
    )
as mla_acl_roles
join mla_acl_resources
on mla_acl_resources.id = mla_acl_roles.resource_id
where 1
" ;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	public function getNoneResourcesOfRole($role_id,$limit,$offset)
	{
		$sql_ResouresOfRole =
		"
	   SELECT
		mla_acl_role_resource.resource_id
	FROM mla_acl_role_resource
	LEFT JOIN mla_acl_roles
		on mla_acl_roles.id = mla_acl_role_resource.role_id
	WHERE 1
	AND mla_acl_roles.id = " . $role_id;
	
		$sql =
		"select
		*
		from mla_acl_resources
		where mla_acl_resources.id Not in
		(".
			$sql_ResouresOfRole
			.
			")";
		
			$sql = $sql. " order by mla_acl_resources.resource";
	
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
	
	/**
	 * 
	 * @param unknown $resource
	 */
	public function isResourceExits($resource) {
		$adapter = $this->tableGateway->adapter;
		
		$where = array (
				'resource=?' => $resource 
		);
		
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		
		$select->from ( array (
				't1' => 'mla_acl_resources' 
		) );
		$select->where ( $where );
		
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$results = $statement->execute ();
		
		if ($results->count () > 0) {
			return true;
		} else {
			return false;
		}
	}
}