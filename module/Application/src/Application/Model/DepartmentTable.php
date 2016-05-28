<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Application\Model\Department;

class DepartmentTable {
	
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
	public function add(Department $input) {
		
		$data = array (
				'name' => $input->name,
				'short_name' => $input->short_name,
				'description' => $input->description,
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
	public function update(Department $input, $id) {
		$data = array (
				'name' => $input->name,
				'short_name' => $input->short_name,
				'description' => $input->description,
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
	
	public function getDepartments()
	{
	
		$sql =
"select T1.*, T2.total_members from mla_departments as T1
left join
(select tt1.department_id, count(tt1.user_id) as total_members from mla_departments_members as tt1
group by tt1.department_id) as T2
ON T1.id = T2.department_id" ;
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	public function getLimitDepartments($limit,$offset)
	{
	
		$sql =
		"select T1.*, T2.total_members from mla_departments as T1
left join
(select tt1.department_id, count(tt1.user_id) as total_members from mla_departments_members as tt1
group by tt1.department_id) as T2
ON T1.id = T2.department_id
limit " . $limit . ' offset '. $offset ;
		
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}

}