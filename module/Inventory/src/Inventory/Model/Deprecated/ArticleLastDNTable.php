<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\ArticleLastDN;

class ArticleLastDNTable {
	protected $tableGateway;
	
	/**
	 * 
	 * @param TableGateway $tableGateway
	 */
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * 
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 */
	public function get($id) {
		$id = ( int ) $id;
		
		$where = 'id = ' . $id;
		$rowset = $this->tableGateway->select ( $where );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	
	/**
	 * 
	 * @param ArticleLastDN $input
	 * @return number
	 */
	public function add(ArticleLastDN $input) {
		
		$result = $this->getArticleID($input->article_id);
		
		if($result !== null):
			$this->update($input, $result->id);
			return $result->id;
		else:
			$data = array (
					'article_id' => $input->article_id,
					'last_workflow_id' => $input->last_workflow_id,
			);
			$this->tableGateway->insert ( $data );
			return $this->tableGateway->lastInsertValue;
		endif;
	}
	
	/**
	 * 
	 * @param ArticleLastDN $input
	 * @param unknown $id
	 */
	public function update(ArticleLastDN $input, $id) {
		$data = array (
				'article_id' => $input->article_id,
				'last_workflow_id' => $input->last_workflow_id,
		);
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function getArticleID($id) {
		$adapter = $this->tableGateway->adapter;
	
	
		$sql = "
SELECT * from mla_articles_last_dn as TT1
WHERE TT1.article_id =". $id;
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
		if(count($resultSet) >0 ):
			return $resultSet->current();
		else:
			return null;
		endif;
	}
}