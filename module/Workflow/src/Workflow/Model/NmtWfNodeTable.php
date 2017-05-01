<?php

namespace Workflow\Model;

use Workflow\Model\NmtWfNode;
use Zend\Db\TableGateway\TableGateway;

/**
 *
 * @author nmt
 *        
 */
class NmtWfNodeTable {
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
	 * @return array|ArrayObject|NULL
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
	 * @param NmtWfCase $input        	
	 * @return number
	 */
	public function add(NmtWfNode $input) {
		$data = array (
				'workflow_id' => $input->workflow_id,
				'node_id' => $input->node_id,
				'node_name' => $input->node_name,
				'node_parent_id' => $input->node_parent_id,
				'node_type' => $input->node_type,
				'node_connection_type' => $input->node_connection_type,
				
				'path' => $input->path,
				'path_depth' => $input->path_depth,
				'status' => $input->status,
				'remarks' => $input->remarks,
				
				'node_created_by' => date ( 'Y-m-d H:i:s' ),
				'node_created_by' => $input->node_created_by,
				'place_id' => $input->place_id,
				'transition_id' => $input->transition_id 
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 *
	 * @param unknown $seletect_items        	
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function setSelectedCartItemsAsNotified($seletect_items) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "
		update
		(
		   mla_delivery_cart
		)
		set  mla_delivery_cart.status  = 'NOTIFIED'
		where 1
		and mla_delivery_cart.id  IN " . $seletect_items;
		
		// echo ($sql);
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
}