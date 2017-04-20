<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\ResultSet\ResultSet;

/**
 * 
 * @author nmt
 *
 */
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
	 * @param unknown $role_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNoneMembersOfRole($role_id)
	{
		$sql_MemberOfRole = 	
	"
	SELECT
		mla_users.id
	FROM nmt_application_acl_user_role 
	LEFT JOIN mla_users
		on mla_users.id = nmt_application_acl_user_role.user_id
	WHERE 1
	AND nmt_application_acl_user_role.role_id = " . $role_id;	
		
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
		
		$select->from(array('t1'=>'nmt_application_acl_user_role'));
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
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNoneResourcesOfRole($role_id,$limit,$offset)
	{
		$sql_ResouresOfRole =
		"
		SELECT
			nmt_application_acl_role_resource.resource_id
		FROM nmt_application_acl_role_resource
	
		LEFT JOIN nmt_application_acl_role
		ON nmt_application_acl_role.id = nmt_application_acl_role_resource.role_id
		WHERE 1
	
	    AND nmt_application_acl_role.id =
	    (
	        select
				nmt_application_acl_role.id
			from nmt_application_acl_role
			where nmt_application_acl_role.role='member'
		)
		OR nmt_application_acl_role.id = " . $role_id;
		
		$sql =
		
		"
		SELECT
		*
		FROM nmt_application_acl_resource
		WHERE nmt_application_acl_resource.id NOT IN
		(".
			$sql_ResouresOfRole
		.
		")";
		
		$sql = $sql. " ORDER by nmt_application_acl_resource.module, nmt_application_acl_resource.controller, nmt_application_acl_resource.action";
		
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
		
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
		
		$result = $statement->execute();
		
		$resultSet = new ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}

}