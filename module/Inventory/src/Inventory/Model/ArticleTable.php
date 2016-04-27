<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\Article;

class ArticleTable {
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
	
	/*
	 * public $id;
	 * public $name;
	 * public $description;
	 * public $keywords;
	 *
	 * public $type;
	 * public $code;
	 * public $barcode;
	 *
	 * public $created_on;
	 * public $created_by;
	 * public $status;
	 * public $visibility;
	 * public $remarks;
	 */
	public function add(Article $input) {
		
		$data = array (
				'name' => $input->name,
				'description' => $input->description,
				'keywords' => $input->keywords,
				'type' => $input->type,
				'code' => $input->code,
				
				'barcode' => $input->barcode,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				'status' => $input->status,
				'visibility' => $input->visibility,
				'remarks' => $input->remarks 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/*
	 * 
	 */
	public function update(Article $input, $id) {
		$data = array (
				'name' => $input->name,
				'description' => $input->description,
				'keywords' => $input->keywords,
				'type' => $input->type,
				'code' => $input->code,
				
				'barcode' => $input->barcode,
				'created_by' => $input->created_by,
				'status' => $input->status,
				'visibility' => $input->visibility,
				'remarks' => $input->remarks 
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	

	public function getLimitArticles($limit,$offset){
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
				
		$select->from('mla_articles');
		$select->limit($limit)->offset($offset);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		// array
		return $results;
	}
	
	/**
	 * 
	 * @param unknown $id
	 */		
	public function getArticlesOf($id)
	{
	
		$sql = "
		select T1.*, T2.department_id from mla_articles as T1
left join mla_departments_members as T2
on T2.user_id = T1.created_by
Where T1.created_by = " . $id;
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id
	 */
	public function getLimittedArticlesOf($id,$limit,$offset)
	{
	
		$sql = "
		select T1.*, T2.department_id from mla_articles as T1
left join mla_departments_members as T2
on T2.user_id = T1.created_by
Where T1.created_by = " . $id . 
" limit " . $limit . ' offset '. $offset;	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown User $id
	 */
	public function getArticlesOfMyDepartment($id)
	{
	
		$sql ="
		SELECT * FROM
		(select T1.*, T2.department_id from mla_articles as T1
				left join mla_departments_members as T2
				on T2.user_id = T1.created_by) AS TT1
				where TT1.department_id IN (SELECT department_id from mla_departments_members
						where user_id = " . $id .")";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown User $id
	 */
	public function getLimitedArticlesOfMyDepartment($id,$limit,$offset)
	{
	
		$sql ="
		SELECT * FROM
		(select T1.*, T2.department_id from mla_articles as T1
				left join mla_departments_members as T2
				on T2.user_id = T1.created_by) AS TT1
				where TT1.department_id IN (select department_id from mla_departments_members
						where user_id = " . $id .") limit " . $limit . ' offset '. $offset; 
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
}