<?php

namespace Workflow\Model;

use Zend\Db\TableGateway\TableGateway;
use Workflow\Model\NmtWfPlace;

class NmtWfPlaceTable {
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
	 * @param NmtWfPlace $input
	 * @return number
	 */
	public function add(NmtWfPlace $input) {
		$data = array (
				'workflow_id' => $input->workflow_id,
				'place_id' => $input->place_id,
				'place_type' => $input->place_type,
				'place_name' => $input->place_name,
				'place_description' => $input->place_description,
				'place_created_on' => date ( 'Y-m-d H:i:s' ),
				'place_created_by' => $input->place_created_by,
	);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
}