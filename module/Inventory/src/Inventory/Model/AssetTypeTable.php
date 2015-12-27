<?php

namespace Inventory\Model;
use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetType;

class AssetTypeTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/*
	 *
	 */
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	

	public function add(AssetType $aType)
	{
		$data=array(
				'type'=>$aType->type,
				'description'=>$aType->description,
				'created_on'=>date('Y-m-d H:i:s')
		);
		$resultSet = $this->tableGateway->insert($data);
	}

}