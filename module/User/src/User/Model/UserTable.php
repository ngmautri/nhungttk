<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\Pdo\Result;

class UserTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/*
	 *
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/*
	 *
	 */
	public function getUserByID($id) {
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
	 * @param unknown $email
	 * @return NULL|unknown
	 */
	public function getUserByEmail($email) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		
		$where = 'email =\'' . $email . '\'';
		
		$select->from ( 'mla_users' );
		$select->where ( $where );
		
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$results = $statement->execute ();
		
		$row = $results->current ();
		
		if (! $row) {
			return null;
		}
		return $row;
	}
	
		
	/**
	 *
	 * @param unknown $user_id        	
	 * @param unknown $new_password        	
	 * @return NULL
	 */
	public function changePassword($user_id, $new_password) {
		$sql = "
	update
	mla_users
	set password = '" . $new_password . "'
	where 1
	AND id = " . $user_id;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return null;
	}
	
	/*
	 *
	 */
	public function add(User $input) {
		$data = array (
				'title' => $input->title,
				'firstname' => $input->firstname,
				'lastname' => $input->lastname,
				'password' => $input->password,
				'email' => $input->email,
				'role' => $input->role,
				'registration_key' => $input->registration_key,
				'confirmed' => $input->confirmed,
				'register_date' => $input->register_date,
				'lastvisit_date' => $input->lastvisit_date,
				'register_date' => $input->register_date,
				'block' => $input->block 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/*
	 *
	 *
	 */
	public function delete($id) {
	}
	
	/**
	 *
	 * @param unknown $email        	
	 */
	public function getUserDepartment($id) {
		$sql = "
     select
		mla_users.id,
        mla_users.title,
        mla_users.firstname, 
        mla_users.lastname,
        mla_users.email, 
        mla_departments_members.*
    from mla_users
    join 
	(
    select
			mla_departments_members.user_id,
			mla_departments_members.department_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status,
            mla_departments.article_root_category
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
    )
    as mla_departments_members
    on mla_users.id = mla_departments_members.user_id
    
    where 1 AND mla_users.id = " . $id;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if (count ( $resultSet ) > 0) {
			return $resultSet->current ();
		} else {
			return null;
		}
	}
	
	/**
	 *
	 * @param unknown $email        	
	 */
	public function getUserDepartmentByEmail($email) {
		$sql = "
 /**USER-DEPARTMENT beginns*/
    select
		mla_users.id,
        mla_users.title,
        mla_users.firstname,
        mla_users.lastname,
        mla_users.email,
        mla_departments_members_1.*
    from mla_users
    join
	(	select
			mla_departments_members.user_id,
			mla_departments_members.department_id,
             mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
			join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1
    on mla_users.id = mla_departments_members_1.user_id
	
    where 1 AND mla_users.email = '" . $email . "'";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if (count ( $resultSet ) > 0) {
			return $resultSet->current ();
		} else {
			return null;
		}
	}
	
	/**
	 *
	 * @param unknown $user_id        	
	 * @return array
	 */
	public function getRoleByUserId($user_id) {
		$sql = "
		select
				nmt_application_acl_user_role. *,
				nmt_application_acl_role.role as role,
				nmt_application_acl_role.parent_id,
				nmt_application_acl_role.path
		from nmt_application_acl_user_role
		join nmt_application_acl_role
		on nmt_application_acl_role.id = nmt_application_acl_user_role.role_id
        where 1 AND nmt_application_acl_user_role.user_id = " . $user_id;
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result);
		return $resultSet->toArray();
		
	}
	
	/**
	 * 
	 * @param unknown $company_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNoneMembersOfCompany($company_id)
	{
		$sql_MemberOfCompany =
		"
	SELECT
		mla_users.id
	FROM nmt_application_company_member
	LEFT JOIN mla_users
		on mla_users.id = nmt_application_company_member.user_id
	WHERE 1
	AND nmt_application_company_member.company_id = " . $company_id;
		
		$sql =
	"SELECT
	*
	FROM mla_users
	where mla_users.id NOT IN
		(".
		$sql_MemberOfCompany
		.
		")";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
		
		$result = $statement->execute();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	
	
}