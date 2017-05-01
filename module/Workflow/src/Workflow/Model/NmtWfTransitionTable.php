<?php

namespace Workflow\Model;

use Zend\Db\TableGateway\TableGateway;
use Workflow\Model\NmtWfNode;

class NmtWfTransitionTable {
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
	
}