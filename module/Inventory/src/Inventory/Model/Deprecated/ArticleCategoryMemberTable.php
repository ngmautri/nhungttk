<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Inventory\Model\ArticleCategoryMember;

class ArticleCategoryMemberTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 *
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 * @return ArrayObject|NULL
	 */
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
	 * @param ArticleCategoryMember $input        	
	 * @return number
	 */
	public function add(ArticleCategoryMember $input) {
		$data = array (
				'article_id' => $input->article_id,
				'article_cat_id' => $input->article_cat_id,
				'updated_by' => $input->updated_by 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param ArticleCategoryMember $input        	
	 * @param unknown $id        	
	 */
	public function update(ArticleCategoryMember $input, $id) {
		$data = array (
				'article_id' => $input->article_id,
				'article_cat_id' => $input->article_cat_id 
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
	 * @param unknown $article_id        	
	 * @param unknown $article_cat_id        	
	 * @return boolean
	 */
	public function isMember($article_id, $article_cat_id) {
		$adapter = $this->tableGateway->adapter;
		
		$where = array (
				'article_id=?' => $article_id,
				'article_cat_id=?' => $article_cat_id 
		);
		
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		
		$select->from ( array (
				't1' => 'mla_articles_categories_members' 
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
	
	/**
	 *
	 * @param unknown $role_id        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getNoneMembersOfCategory($article_cat_id) {
		$sql_MemberOfCat = "
		SELECT
		mla_articles.id
		FROM mla_articles_categories_members
		LEFT JOIN mla_articles
			on mla_articles.id = mla_articles_categories_members.article_id
		WHERE 1
		AND mla_articles_categories_members.article_cat_id " . $article_cat_id;
		
		$sql = "select
		*
		from mla_articles
		where mla_articles.id Not in
		(" . $sql_MemberOfCat . ")";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query ( $sql );
		
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
}