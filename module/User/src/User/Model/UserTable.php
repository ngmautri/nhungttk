<?php

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

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
	
	public function getUserByEmail($email) {
		
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
		$where = 'email =\''.$email.'\'';
			
		$select->from('mla_users');
		$select->where($where);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		return $results;
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
				'block' => $input->block,				 
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
}