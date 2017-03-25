<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet;
use Inventory\Model\MlaArticleDepartment;

class MlaArticleDepartmentTable {
			
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
	
	
	public function add(MlaArticleDepartment $input) {
		$data = array (
				'article_id' => $input->article_id,
				'department_id' => $input->department_id,
				'updated_by' => $input->updated_by,
				'updated_on' => date ( 'Y-m-d H:i:s' ),
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	public function isExits($id)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'id=?'		=> $id,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_articles_categories'));
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