<?php

namespace HR\Model;

use Zend\Db\TableGateway\TableGateway;
use HR\Model\Vendor;	


class EmployeeTable {
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	
	public function get($id){
		
		$id  = (int) $id;
		
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id");
		}
		return $row;
	}
	
	/**
	public $id;
	public $name;
	public $keywords;
	public $status;
	public $created_on;
	public $created_by;
	 * 
	 *
	 * @param Delivery $input
	 */
	public function add(Vendor $input) {
		$data = array (
				'name' => $input->name,
				'keywords' => $input->keywords,
				'status' => $input->status,
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	public function update(Vendor $input, $id) {
		
		$data = array (
				'name' => $input->name,
				'keywords' => $input->keywords,
				'status' => $input->status,
				'created_by' => $input->created_by,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				);	
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	/**
	 * 
	 * @return number
	 */
	public function getTotalEmployee()
	{
	
		$sql = "
		select 
		count(*) as total_employee
		from hr_employee
		where 1 ";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return (int)$resultSet->current()->total_employee;
	}
	
	/**
	 * 
	 * @param unknown $limit
	 * @param unknown $offset
	 * @return number
	 */
	public function getEmployees($limit,$offset)
	{
	
		$sql = "
		select
			hr_employee.*,
            hr_employee_picture.id as sp_pic_id
		from hr_employee
        
        left join
(
				select
				*
				from 
                (
                select * from
					hr_employee_picture
                    order by hr_employee_picture.uploaded_on desc
                )
                as hr_employee_picture
				group by hr_employee_picture.employee_id
)
as hr_employee_picture
on hr_employee_picture.employee_id = hr_employee.id


		where 1
				
				";
	
		
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
	

}