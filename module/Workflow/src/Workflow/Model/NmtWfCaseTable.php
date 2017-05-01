<?php

namespace Workflow\Model;

use Zend\Db\TableGateway\TableGateway;
use Workflow\Model\NmtWfCase;

class NmtWfCaseTable {
	
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
	 * @param NmtWfCase $input
	 * @return number
	 */
	public function add(NmtWfCase $input) {
		$data = array (
				'case_id' => $input->case_id,
				'workflow_id' => $input->workflow_id,
				'context' => $input->context,
				'case_status' => $input->case_status,
				'start_date' => $input->start_date,
				'end_date' => $input->end_date,
				'case_created_on' => date ( 'Y-m-d H:i:s' ),
				'case_created_by' => $input->case_created_by
		);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
}