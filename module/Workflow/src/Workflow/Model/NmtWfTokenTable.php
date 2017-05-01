<?php

namespace Workflow\Model;

use Zend\Db\TableGateway\TableGateway;
use Workflow\Model\NmtWfToken;

class NmtWfTokenTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
/* 	public $token_id;
	public $case_id;
	public $place_id;
	public $workflow_id;
	public $token_status;
	public $enabled_date;
	public $token_enabled_by;
	public $cancelled_date;
	public $token_cancelled_by;
	public $comsumed_date;
	public $token_consumed_by;
	public $node_id; */
	
	/**
	 * 
	 * @param NmtWfToken $input
	 * @return number
	 */
	public function add(NmtWfToken $input) {
		$data = array (
				'token_id' => $input->token_id,
				'case_id' => $input->case_id,
				'place_id' => $input->place_id,
				'token_status' => $input->ctoken_status,
				'token_status' => $input->token_status,
				'enabled_date' => $input->enabled_date,
				'token_enabled_by' => $input->token_enabled_by,
				'cancelled_date' => $input->cancelled_date,
				'token_cancelled_by' => $input->token_cancelled_by,
				'comsumed_date' => $input->comsumed_date,
				'token_consumed_by' => $input->token_consumed_by,
				'node_id' => $input->node_id,
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
}