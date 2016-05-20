<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\ArticleMovement;

class ArticleMovementTable {
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
	
	public function getMovements($article_id)
	{
		
		$adapter = $this->tableGateway->adapter;
		$sql = "select * from mla_articles_movements WHERE 1";
		$sql =$sql. " AND mla_articles_movements.article_id = " . $article_id;
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		
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
	 * public $id;
	 * public $movement_date;
	 * public $article_id;
	 *
	 * public $flow;
	 * public $quantity;
	 * public $reason;
	 * public $requester;
	 * public $comment;
	 * public $created_on;
	 * public $created_by;
	 *
	 * public $dn_item_id;
	 * public $pr_item_id;
	 *
	 *
	 * @param SparepartMovement $input        	
	 */
	public function add(ArticleMovement $input) {
		$data = array (
				'movement_date' => $input->movement_date,
				'article_id' => $input->article_id,
				
				'flow' => $input->flow,
				'quantity' => $input->quantity,
				'reason' => $input->reason,
				'requester' => $input->requester,
				
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				'dn_item_id' => $input->dn_item_id,
				'pr_item_id' => $input->pr_item_id 
		);
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param SparepartPicture $input        	
	 * @param unknown $id        	
	 */
	public function update(ArticleMovement $input, $id) {
		$data = array (
				'movement_date' => $input->movement_date,
				'article_id' => $input->article_id,
				
				'flow' => $input->flow,
				'quantity' => $input->quantity,
				'reason' => $input->reason,
				'requester' => $input->requester,
				
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				'dn_item_id' => $input->dn_item_id,
				'pr_item_id' => $input->pr_item_id 
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
}