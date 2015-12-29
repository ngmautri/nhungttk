<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetType;

class AssetTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/*
	 *
	 */
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}

}